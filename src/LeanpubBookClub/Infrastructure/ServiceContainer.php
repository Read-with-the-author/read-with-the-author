<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Application\Email\Mailer;
use LeanpubBookClub\Application\Email\SendEmail;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\RequestAccess\GenerateAccessToken;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\AccessWasGrantedToMember;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasImported;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;
use LeanpubBookClub\Domain\Model\Session\AttendanceWasCancelledBecauseSessionWasCancelled;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;
use Test\Acceptance\FakeClock;
use Test\Acceptance\IndividualPurchasesInMemory;

abstract class ServiceContainer
{
    protected ?EventDispatcher $eventDispatcher = null;

    protected ?ApplicationInterface $application = null;
    protected ?Sessions $upcomingSessions = null;
    private ?Clock $clock = null;
    protected ?MemberRepository $memberRepository = null;
    protected ?PurchaseRepository $purchaseRepository = null;
    protected ?SessionRepository $sessionRepository = null;
    private ?IndividualPurchasesInMemory $individualPurchases = null;
    protected ?Members $members = null;

    protected function clock(): Clock
    {
        if ($this->clock === null) {
            $this->clock = new FakeClock();
        }

        return $this->clock;
    }

    public function eventDispatcher(): EventDispatcher
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcherWithSubscribers();

            $this->registerEventSubscribers($this->eventDispatcher);
        }

        Assert::that($this->eventDispatcher)->isInstanceOf(EventDispatcher::class);

        return $this->eventDispatcher;
    }

    protected function registerEventSubscribers(EventDispatcherWithSubscribers $eventDispatcher): void
    {
        $eventDispatcher->subscribeToSpecificEvent(
            MemberRequestedAccess::class,
            [$this->accessPolicy(), 'whenMemberRequestedAccess']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            PurchaseWasClaimed::class,
            [$this->accessPolicy(), 'whenPurchaseWasClaimed']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            PurchaseWasImported::class,
            [$this->accessPolicy(), 'whenPurchaseWasImported']
        );

        $eventDispatcher->subscribeToSpecificEvent(
            AccessWasGrantedToMember::class,
            [new GenerateAccessToken($this->application()), 'whenAccessWasGrantedToMember']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AnAccessTokenWasGenerated::class,
            [$this->sendEmail(), 'whenAnAccessTokenWasGenerated']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AttendeeRegisteredForSession::class,
            [$this->sendEmail(), 'whenAttendeeRegisteredForSession']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AttendanceWasCancelledBecauseSessionWasCancelled::class,
            [$this->sendEmail(), 'whenAttendanceWasCancelledBecauseSessionWasCancelled']
        );
    }

    protected function individualPurchases(): IndividualPurchases
    {
        if ($this->individualPurchases === null) {
            $this->individualPurchases = new IndividualPurchasesInMemory();
        }

        return $this->individualPurchases;
    }

    public function application(): ApplicationInterface
    {
        if ($this->application === null) {
            $this->application = new Application(
                $this->memberRepository(),
                $this->eventDispatcher(),
                $this->purchaseRepository(),
                $this->sessionRepository(),
                $this->clock(),
                $this->sessions(),
                $this->individualPurchases(),
                $this->accessTokenGenerator(),
                $this->members()
            );
        }

        return $this->application;
    }

    private function accessPolicy(): AccessPolicy
    {
        return new AccessPolicy(
            $this->application(),
            $this->purchaseRepository(),
            $this->memberRepository(),
            $this->eventDispatcher()
        );
    }

    abstract protected function purchaseRepository(): PurchaseRepository;

    abstract protected function sessionRepository(): SessionRepository;

    abstract protected function memberRepository(): MemberRepository;

    abstract protected function sessions(): Sessions;

    abstract protected function mailer(): Mailer;

    private function accessTokenGenerator(): AccessTokenGenerator
    {
        return new RealUuidAccessTokenGenerator();
    }

    abstract protected function members(): Members;

    protected function authorTimeZone(): TimeZone
    {
        return TimeZone::fromString('Europe/Amsterdam');
    }

    private function sendEmail(): SendEmail
    {
        return new SendEmail($this->mailer(), $this->eventDispatcher(), $this->members(), $this->sessions());
    }
}
