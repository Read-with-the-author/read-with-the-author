<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\AccessPolicy;
use LeanpubBookClub\Application\Application;
use LeanpubBookClub\Application\EventDispatcherWithSubscribers;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;

final class ServiceContainerForAcceptanceTesting
{
    private ?Application $application = null;
    private ?EventDispatcherSpy $eventDispatcher = null;
    private ?MemberRepository $memberRepository = null;
    private ?LeanpubSalesInMemory $leanpubSales = null;

    public function eventDispatcher(): EventDispatcherSpy
    {
        if ($this->eventDispatcher === null) {
            $eventDispatcher = new EventDispatcherWithSubscribers();

            $this->eventDispatcher = new EventDispatcherSpy($eventDispatcher);

            $eventDispatcher->addSubscriber(
                MemberRequestedAccess::class,
                [new AccessPolicy($this->application(), $this->leanpubSales()), 'whenMemberRequestedAccess']
            );
        }

        return $this->eventDispatcher;
    }

    public function application(): Application
    {
        if ($this->application === null) {
            $this->application = new Application(
                $this->memberRepository(),
                $this->eventDispatcher()
            );
        }

        return $this->application;
    }

    private function memberRepository(): MemberRepository
    {
        if ($this->memberRepository === null) {
            $this->memberRepository = new MemberRepositoryInMemory();
        }

        return $this->memberRepository;
    }

    public function leanpubSales(): LeanpubSalesInMemory
    {
        if ($this->leanpubSales === null) {
            $this->leanpubSales = new LeanpubSalesInMemory();
        }

        return $this->leanpubSales;
    }
}
