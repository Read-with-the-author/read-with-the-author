<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Assert\Assert;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Session\AttendeeCancelledTheirAttendance;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;
use LeanpubBookClub\Domain\Model\Session\UrlForCallWasUpdated;
use LeanpubBookClub\Infrastructure\ServiceContainer;

final class ServiceContainerForAcceptanceTesting extends ServiceContainer
{
    private ?EventDispatcherSpy $eventDispatcherSpy = null;

    private ?MailerSpy $mailer = null;

    public function authorTimeZone(): TimeZone
    {
        return parent::authorTimeZone();
    }

    public function setCurrentDate(string $date): void
    {
        $clock = $this->clock();
        Assert::that($clock)->isInstanceOf(FakeClock::class);
        /** @var $clock FakeClock */

        $clock->setCurrentDate($date);
    }

    public function setCurrentTime(string $dateTime): void
    {
        $clock = $this->clock();
        Assert::that($clock)->isInstanceOf(FakeClock::class);
        /** @var $clock FakeClock */

        $clock->setCurrentTime($dateTime);
    }

    protected function registerEventSubscribers(EventDispatcherWithSubscribers $eventDispatcher): void
    {
        parent::registerEventSubscribers($eventDispatcher);

        $eventDispatcher->subscribeToAllEvents([$this->eventDispatcherSpy(), 'notify']);

        // Test-specific listeners:
        $eventDispatcher->subscribeToSpecificEvent(
            SessionWasPlanned::class,
            [$this->sessions(), 'whenSessionWasPlanned']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            UrlForCallWasUpdated::class,
            [$this->sessions(), 'whenUrlForCallWasUpdated']
        );

        $eventDispatcher->subscribeToSpecificEvent(
            AttendeeRegisteredForSession::class,
            [$this->sessions(), 'whenAttendeeRegisteredForSession']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AttendeeCancelledTheirAttendance::class,
            [$this->sessions(), 'whenAttendeeCancelledTheirAttendance']
        );

        $eventDispatcher->subscribeToSpecificEvent(
            MemberRequestedAccess::class,
            [$this->members(), 'whenMemberRequestedAccess']
        );
        $eventDispatcher->subscribeToSpecificEvent(
            AnAccessTokenWasGenerated::class,
            [$this->members(), 'whenAnAccessTokenWasGenerated']
        );
    }

    protected function purchaseRepository(): PurchaseRepository
    {
        if ($this->purchaseRepository === null) {
            $this->purchaseRepository = new PurchaseRepositoryInMemory();
        }

        return $this->purchaseRepository;
    }

    protected function sessionRepository(): SessionRepository
    {
        if ($this->sessionRepository === null) {
            $this->sessionRepository = new SessionRepositoryInMemory();
        }

        return $this->sessionRepository;
    }

    protected function memberRepository(): MemberRepository
    {
        if ($this->memberRepository === null) {
            $this->memberRepository = new MemberRepositoryInMemory();
        }

        return $this->memberRepository;
    }

    protected function members(): MembersInMemory
    {
        if ($this->members ===  null) {
            $this->members = new MembersInMemory();
        }

        Assert::that($this->members)->isInstanceOf(MembersInMemory::class);

        return $this->members;
    }

    protected function sessions(): SessionsInMemory
    {
        if ($this->upcomingSessions === null) {
            $this->upcomingSessions = new SessionsInMemory();
        }

        Assert::that($this->upcomingSessions)->isInstanceOf(SessionsInMemory::class);

        return $this->upcomingSessions;
    }

    public function eventDispatcherSpy(): EventDispatcherSpy
    {
        if ($this->eventDispatcherSpy === null) {
            $this->eventDispatcherSpy = new EventDispatcherSpy();
        }

        return $this->eventDispatcherSpy;
    }

    public function individualPurchases(): IndividualPurchasesInMemory
    {
        $individualPurchases = parent::individualPurchases();
        Assert::that($individualPurchases)->isInstanceOf(IndividualPurchasesInMemory::class);
        /** @var $individualPurchases IndividualPurchasesInMemory */

        return $individualPurchases;
    }

    public function mailer(): MailerSpy
    {
        if ($this->mailer === null) {
            $this->mailer = new MailerSpy();
        }

        return $this->mailer;
    }
}
