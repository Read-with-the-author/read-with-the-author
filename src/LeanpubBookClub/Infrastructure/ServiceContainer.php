<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AssetPublisher;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;
use Test\Acceptance\AssetPublisherInMemory;
use Test\Acceptance\FakeClock;
use Test\Acceptance\GetBookSummaryInMemory;
use Test\Acceptance\IndividualPurchasesInMemory;
use Test\Acceptance\MemberRepositoryInMemory;
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

    protected function clock(): Clock
    {
        if ($this->clock === null) {
            $this->clock = new FakeClock();
        }

        return $this->clock;
    }

    protected function eventDispatcher(): EventDispatcher
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
                $this->assetPublisher()
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

    protected function getBookSummary(): GetBookSummary
    {
        return new GetBookSummaryInMemory();
    }

    protected function assetPublisher(): AssetPublisher
    {
        return new AssetPublisherInMemory();
    }
}
