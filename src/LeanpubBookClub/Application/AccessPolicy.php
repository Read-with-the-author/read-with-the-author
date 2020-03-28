<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;

final class AccessPolicy
{
    private Application $application;

    private PurchaseRepository $purchaseRepository;

    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    public function __construct(
        Application $application,
        PurchaseRepository $purchaseRepository,
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->application = $application;
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function whenMemberRequestedAccess(MemberRequestedAccess $event): void
    {
        $this->application->claimPurchase($event->leanpubInvoiceId());
    }

    public function whenPurchaseWasClaimed(PurchaseWasClaimed $event): void
    {
        $this->application->grantAccess($event->memberId());
    }
}
