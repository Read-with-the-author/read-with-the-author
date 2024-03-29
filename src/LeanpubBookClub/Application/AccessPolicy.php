<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasImported;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseWasClaimed;

final class AccessPolicy
{
    private ApplicationInterface $application;

    private PurchaseRepository $purchaseRepository;

    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    public function __construct(
        ApplicationInterface $application,
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

    public function whenPurchaseWasImported(PurchaseWasImported $event): void
    {
        if ($this->memberRepository->exists($event->leanpubInvoiceId())) {
            // There was a member waiting for access, let's grant it now
            $this->application->grantAccess($event->leanpubInvoiceId());
        }
    }
}
