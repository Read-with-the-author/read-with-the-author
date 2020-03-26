<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;

final class ServiceContainerForAcceptanceTesting
{
    private ?Application $application = null;
    private ?EventDispatcherSpy $eventDispatcher = null;
    private ?MemberRepository $memberRepository = null;
    private ?PurchaseRepository $purchaseRepository = null;
    private ?SessionRepository $sessionRepository = null;
    private ?FakeClock $clock = null;
    private ?UpcomingSessionsInMemory $upcomingSessions = null;
    private ?IndividualPurchasesInMemory $individualPurchases = null;

    public function eventDispatcher(): EventDispatcherSpy
    {
        if ($this->eventDispatcher === null) {
            $eventDispatcher = new EventDispatcherWithSubscribers();

            $this->eventDispatcher = new EventDispatcherSpy($eventDispatcher);

            $eventDispatcher->addSubscriber(
                MemberRequestedAccess::class,
                [$this->accessPolicy(), 'whenMemberRequestedAccess']
            );
            $eventDispatcher->addSubscriber(
                PurchaseWasClaimed::class,
                [$this->accessPolicy(), 'whenPurchaseWasClaimed']
            );

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

        return $this->eventDispatcher;
    }

    public function application(): Application
    {
        if ($this->application === null) {
            $this->application = new Application(
                $this->memberRepository(),
                $this->eventDispatcher(),
                $this->purchaseRepository(),
                $this->sessionRepository(),
                $this->clock(),
                $this->upcomingSessions(),
                $this->individualPurchases()
            );
        }

        return $this->application;
    }

    public function setCurrentTime(DateTimeImmutable $currentTime): void
    {
        $this->clock()->setCurrentTime($currentTime);
    }

    private function purchaseRepository(): PurchaseRepository
    {
        if ($this->purchaseRepository === null) {
            $this->purchaseRepository = new PurchaseRepositoryInMemory();
        }

        return $this->purchaseRepository;
    }

    private function memberRepository(): MemberRepository
    {
        if ($this->memberRepository === null) {
            $this->memberRepository = new MemberRepositoryInMemory();
        }

        return $this->memberRepository;
    }

    private function sessionRepository(): SessionRepository
    {
        if ($this->sessionRepository === null) {
            $this->sessionRepository = new SessionRepositoryInMemory();
        }

        return $this->sessionRepository;
    }

    private function upcomingSessions(): UpcomingSessionsInMemory
    {
        if ($this->upcomingSessions === null) {
            $this->upcomingSessions = new UpcomingSessionsInMemory();
        }

        return $this->upcomingSessions;
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

    private function clock(): FakeClock
    {
        if ($this->clock === null) {
            $this->clock = new FakeClock();
        }

        return $this->clock;
    }

    public function individualPurchases(): IndividualPurchasesInMemory
    {
        if ($this->individualPurchases === null) {
            $this->individualPurchases = new IndividualPurchasesInMemory();
        }

        return $this->individualPurchases;
    }
}
