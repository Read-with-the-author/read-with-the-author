<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\ClaimWasDenied;
use LeanpubBookClub\Domain\Model\Purchase\CouldNotFindPurchase;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;

final class Application
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private PurchaseRepository $purchaseRepository;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        PurchaseRepository $purchaseRepository
    ) {
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function importPurchase(ImportPurchase $command): void
    {
        try {
            $purchase = $this->purchaseRepository->getById($command->leanpubInvoiceId());
            // This purchase has already been imported
            return;
        } catch (CouldNotFindPurchase $exception) {
            $purchase = Purchase::import($command->leanpubInvoiceId());

            $this->purchaseRepository->save($purchase);

            $this->eventDispatcher->dispatchAll($purchase->releaseEvents());
        }
    }

    public function requestAccess(RequestAccess $command): MemberId
    {
        $memberId = $this->memberRepository->nextIdentity();

        $member = Member::requestAccess($memberId, $command->emailAddress(), $command->leanpubInvoiceId());

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());

        return $member->memberId();
    }

    public function claimPurchase(LeanpubInvoiceId $invoiceId, MemberId $memberId): void
    {
        try {
            $purchase = $this->purchaseRepository->getById($invoiceId);
        } catch (CouldNotFindPurchase $exception) {
            $this->eventDispatcher->dispatch(new ClaimWasDenied($memberId, $invoiceId, 'invalid_purchase_id'));
            return;
        }

        $purchase->claim($memberId);

        $this->purchaseRepository->save($purchase);

        $this->eventDispatcher->dispatchAll($purchase->releaseEvents());
    }

    public function grantAccess(MemberId $memberId): void
    {
        $member = $this->memberRepository->getById($memberId);

        $member->grantAccess();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }
}
