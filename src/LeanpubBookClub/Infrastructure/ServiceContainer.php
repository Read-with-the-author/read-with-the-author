<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use DateTimeZone;
use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AssetPublisher;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Application\Email\Email;
use LeanpubBookClub\Application\Email\Mailer;
use LeanpubBookClub\Application\Email\SendAccessTokenEmail;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\RequestAccess\GenerateAccessToken;
use LeanpubBookClub\Domain\Model\Member\AccessWasGrantedToMember;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;
use Test\Acceptance\AssetPublisherInMemory;
use Test\Acceptance\FakeClock;
use Test\Acceptance\GetBookSummaryInMemory;
use Test\Acceptance\IndividualPurchasesInMemory;
use Test\Acceptance\MemberRepositoryInMemory;
use Test\Acceptance\MembersInMemory;
use Test\Acceptance\PurchaseRepositoryInMemory;
use Test\Acceptance\SessionRepositoryInMemory;
use Test\Acceptance\UpcomingSessionsInMemory;

abstract class ServiceContainer
{
    protected ?EventDispatcher $eventDispatcher = null;

    protected ?ApplicationInterface $application = null;
    private ?UpcomingSessionsInMemory $upcomingSessions = null;
    private ?Clock $clock = null;
    private ?MemberRepository $memberRepository = null;
    private ?PurchaseRepository $purchaseRepository = null;
    private ?SessionRepository $sessionRepository = null;
    private ?IndividualPurchasesInMemory $individualPurchases = null;
    protected ?Mailer $mailer = null;
    private ?Members $members = null;

    protected function clock(): Clock
    {
        if ($this->clock === null) {
            $this->clock = new FakeClock(new DateTimeZone('Europe/Amsterdam'));
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
            AccessWasGrantedToMember::class,
            [new GenerateAccessToken($this->application()), 'whenAccessWasGrantedToMember']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AnAccessTokenWasGenerated::class,
            [new SendAccessTokenEmail($this->mailer(), $this->eventDispatcher()), 'whenAnAccessTokenWasGenerated']
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
                $this->upcomingSessions(),
                $this->individualPurchases(),
                $this->getBookSummary(),
                $this->assetPublisher(),
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

    protected function purchaseRepository(): PurchaseRepository
    {
        // TODO Replace with production implementation
        if ($this->purchaseRepository === null) {
            $this->purchaseRepository = new PurchaseRepositoryInMemory();
        }

        return $this->purchaseRepository;
    }

    protected function sessionRepository(): SessionRepository
    {
        // TODO Replace with production implementation
        if ($this->sessionRepository === null) {
            $this->sessionRepository = new SessionRepositoryInMemory();
        }

        return $this->sessionRepository;
    }

    protected function memberRepository(): MemberRepository
    {
        // TODO Replace with production implementation
        if ($this->memberRepository === null) {
            $this->memberRepository = new MemberRepositoryInMemory();
        }

        return $this->memberRepository;
    }

    protected function upcomingSessions(): UpcomingSessionsInMemory
    {
        // TODO Replace with production implementation
        if ($this->upcomingSessions === null) {
            $this->upcomingSessions = new UpcomingSessionsInMemory();
        }

        return $this->upcomingSessions;
    }

    protected function mailer(): Mailer
    {
        if ($this->mailer === null) {
            $this->mailer = new class implements Mailer {
                // TODO replace with production implementation
                public function send(Email $email): void
                {
                }
            };
        }

        return $this->mailer;
    }

    protected function getBookSummary(): GetBookSummary
    {
        return new GetBookSummaryInMemory();
    }

    protected function assetPublisher(): AssetPublisher
    {
        return new AssetPublisherInMemory();
    }

    private function accessTokenGenerator(): AccessTokenGenerator
    {
        return new RealUuidAccessTokenGenerator();
    }

    private function members(): Members
    {
        if ($this->members ===  null) {
            $this->members = new MembersInMemory();
        }

        return $this->members;
    }
}
