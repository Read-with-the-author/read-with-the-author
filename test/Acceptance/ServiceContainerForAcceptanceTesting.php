<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;
use LeanpubBookClub\Infrastructure\ServiceContainer;

final class ServiceContainerForAcceptanceTesting extends ServiceContainer
{
    public function setCurrentTime(DateTimeImmutable $currentTime): void
    {
        $this->clock()->setCurrentTime($currentTime);
    }

    protected function registerEventSubscribers(EventDispatcherWithSubscribers $eventDispatcher): void
    {
        parent::registerEventSubscribers($eventDispatcher);

        // Test-specific listeners:
        $eventDispatcher->addSubscriber(
            SessionWasPlanned::class,
            [$this->upcomingSessions(), 'whenSessionWasPlanned']
        );
        $eventDispatcher->addSubscriber(
            AttendeeRegisteredForSession::class,
            [$this->upcomingSessions(), 'whenAttendeeRegisteredForSession']
        );
    }

    public function individualPurchases(): IndividualPurchasesInMemory
    {
        return parent::individualPurchases();
    }
}
