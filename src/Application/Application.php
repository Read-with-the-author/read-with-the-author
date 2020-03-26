<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\UpcomingSessions\ListUpcomingSessions;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\ClaimWasDenied;
use LeanpubBookClub\Domain\Model\Purchase\CouldNotFindPurchase;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Session\Session;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;

final class Application
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private PurchaseRepository $purchaseRepository;

    private SessionRepository $sessionRepository;

    private Clock $clock;

    private ListUpcomingSessions $listUpcomingSessions;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        PurchaseRepository $purchaseRepository,
        SessionRepository $sessionRepository,
        Clock $clock,
        ListUpcomingSessions $listUpcomingSessions
    ) {
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchaseRepository = $purchaseRepository;
        $this->sessionRepository = $sessionRepository;
        $this->clock = $clock;
        $this->listUpcomingSessions = $listUpcomingSessions;
    }

    public function importPurchase(ImportPurchase $command): void
    {
        try {
            $this->purchaseRepository->getById($command->leanpubInvoiceId());
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

    public function planSession(PlanSession $command): SessionId
    {
        $sessionId = $this->sessionRepository->nextIdentity();

        $session = Session::plan(
            $sessionId,
            $command->date(),
            $command->description()
        );

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());

        return $session->sessionId();
    }

    /**
     * @return array<UpcomingSession> & UpcomingSession[]
     */
    public function listUpcomingSessions(): array
    {
        return $this->listUpcomingSessions->upcomingSessions($this->clock->currentTime());
    }
}
