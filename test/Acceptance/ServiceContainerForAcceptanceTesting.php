<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Assert\Assert;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Session\AttendeeCancelledTheirAttendance;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;
use LeanpubBookClub\Domain\Model\Session\UrlForCallWasUpdated;
use LeanpubBookClub\Infrastructure\ServiceContainer;

final class ServiceContainerForAcceptanceTesting extends ServiceContainer
{
    private ?EventDispatcherSpy $eventDispatcherSpy = null;

    public function setCurrentTime(string $time): void
    {
        $clock = $this->clock();
        Assert::that($clock)->isInstanceOf(FakeClock::class);

        $clock->setCurrentTime($time);
    }

    public function authorTimeZone(): TimeZone
    {
        return parent::authorTimeZone();
    }

    public function setCurrentDate(string $date): void
    {
        $clock = $this->clock();
        Assert::that($clock)->isInstanceOf(FakeClock::class);

        $clock->setCurrentDate($date);
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
            [$this->sessionCallUrls(), 'whenUrlForCallWasProvided']
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

    protected function members(): MembersInMemory
    {
        $members = parent::members();

        Assert::that($members)->isInstanceOf(MembersInMemory::class);
        /** @var MembersInMemory $members */

        return $members;
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

    protected function sessionCallUrls(): SessionCallUrlsInMemory
    {
        $service = parent::sessionCallUrls();

        Assert::that($service)->isInstanceOf(SessionCallUrlsInMemory::class);
        /** @var $service SessionCallUrlsInMemory */

        return $service;
    }

    public function mailer(): MailerSpy
    {
        if ($this->mailer === null) {
            $this->mailer = new MailerSpy();
        }

        Assert::that($this->mailer)->isInstanceOf(MailerSpy::class);
        return $this->mailer;
    }
}
