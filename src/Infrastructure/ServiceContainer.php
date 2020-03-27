<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use Test\Acceptance\EventDispatcherSpy;
use Test\Acceptance\FakeClock;
use Test\Acceptance\IndividualPurchasesInMemory;
use Test\Acceptance\MemberRepositoryInMemory;
use Test\Acceptance\PurchaseRepositoryInMemory;
use Test\Acceptance\SessionRepositoryInMemory;
use Test\Acceptance\UpcomingSessionsInMemory;

abstract class ServiceContainer
{
    private ?Application $application = null;
    private ?UpcomingSessionsInMemory $upcomingSessions = null;
    private ?FakeClock $clock = null;
    private ?EventDispatcherSpy $eventDispatcher = null;
    private ?MemberRepository $memberRepository = null;
    private ?PurchaseRepository $purchaseRepository = null;
    private ?SessionRepository $sessionRepository = null;
    private ?IndividualPurchasesInMemory $individualPurchases = null;

    protected function clock(): FakeClock
    {
        // TODO Replace with production implementation
        if ($this->clock === null) {
            $this->clock = new FakeClock();
        }

        return $this->clock;
    }

    public function eventDispatcher(): EventDispatcherSpy
    {
        if ($this->eventDispatcher === null) {
            $eventDispatcher = new EventDispatcherWithSubscribers();

            $this->eventDispatcher = new EventDispatcherSpy($eventDispatcher);

            $this->registerEventSubscribers($eventDispatcher);
        }

        Assert::that($this->eventDispatcher)->isInstanceOf(EventDispatcher::class);
        
        return $this->eventDispatcher;
    }

    protected function registerEventSubscribers(EventDispatcherWithSubscribers $eventDispatcher): void
    {
        $eventDispatcher->addSubscriber(
            MemberRequestedAccess::class,
            [$this->accessPolicy(), 'whenMemberRequestedAccess']
        );
        $eventDispatcher->addSubscriber(
            PurchaseWasClaimed::class,
            [$this->accessPolicy(), 'whenPurchaseWasClaimed']
        );
    }

    protected function individualPurchases(): IndividualPurchasesInMemory
    {
        // TODO Replace with production implementation
        if ($this->individualPurchases === null) {
            $this->individualPurchases = new IndividualPurchasesInMemory();
        }

        return $this->individualPurchases;
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
}
