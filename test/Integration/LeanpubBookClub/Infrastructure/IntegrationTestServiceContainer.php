<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\NoOpEventDispatcher;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;

final class IntegrationTestServiceContainer extends ProductionServiceContainer
{
    public function eventDispatcher(): EventDispatcher
    {
        return new NoOpEventDispatcher();
    }

    public function setApplication(ApplicationInterface $application): void
    {
        $this->application = $application;
    }

    public function purchaseRepository(): PurchaseRepository
    {
        return parent::purchaseRepository();
    }

    public function memberRepository(): MemberRepository
    {
        return parent::memberRepository();
    }

    public function sessionRepository(): SessionRepository
    {
        return parent::sessionRepository();
    }
}
