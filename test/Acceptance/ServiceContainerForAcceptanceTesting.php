<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;

final class ServiceContainerForAcceptanceTesting
{
    private ?Application $application = null;
    private ?EventDispatcherSpy $eventDispatcher = null;
    private ?MemberRepository $memberRepository = null;
    private ?PurchaseRepository $purchaseRepository = null;

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
        }

        return $this->eventDispatcher;
    }

    public function application(): Application
    {
        if ($this->application === null) {
            $this->application = new Application(
                $this->memberRepository(),
                $this->eventDispatcher(),
                $this->purchaseRepository()
            );
        }

        return $this->application;
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

    private function accessPolicy(): AccessPolicy
    {
        return new AccessPolicy(
            $this->application(),
            $this->purchaseRepository(),
            $this->memberRepository(),
            $this->eventDispatcher()
        );
    }
}
