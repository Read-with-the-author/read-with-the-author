<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\Importing\PurchaseWasAlreadyImported;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\UpcomingSessions\ListUpcomingSessions;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\ClaimWasDenied;
use LeanpubBookClub\Domain\Model\Purchase\CouldNotFindPurchase;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Session\Session;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;

final class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private PurchaseRepository $purchaseRepository;

    private SessionRepository $sessionRepository;

    private Clock $clock;

    private ListUpcomingSessions $listUpcomingSessions;

    private IndividualPurchases $individualPurchases;

    private GetBookSummary $getBookSummary;

    private AssetPublisher $assetPublisher;

    private AccessTokenGenerator $accessTokenGenerator;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        PurchaseRepository $purchaseRepository,
        SessionRepository $sessionRepository,
        Clock $clock,
        ListUpcomingSessions $listUpcomingSessions,
        IndividualPurchases $individualPurchases,
        GetBookSummary $getBookSummary,
        AssetPublisher $assetPublisher,
        AccessTokenGenerator $accessTokenGenerator
    ) {
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchaseRepository = $purchaseRepository;
        $this->sessionRepository = $sessionRepository;
        $this->clock = $clock;
        $this->listUpcomingSessions = $listUpcomingSessions;
        $this->individualPurchases = $individualPurchases;
        $this->getBookSummary = $getBookSummary;
        $this->assetPublisher = $assetPublisher;
        $this->accessTokenGenerator = $accessTokenGenerator;
    }

    public function importAllPurchases(): void
    {
        foreach ($this->individualPurchases->all() as $purchase) {
            try {
                $this->importPurchase(new ImportPurchase($purchase->invoiceId()));
            } catch (PurchaseWasAlreadyImported $exception) {
                /*
                 * We import the most recent purchases first, so we know we can stop as soon as we encounter an older
                 * purchase that we've already imported.
                 */
                return;
            }
        }
    }

    public function importPurchase(ImportPurchase $command): void
    {
        try {
            $this->purchaseRepository->getById($command->leanpubInvoiceId());

            throw new PurchaseWasAlreadyImported($command->leanpubInvoiceId());
        } catch (CouldNotFindPurchase $exception) {
            $purchase = Purchase::import($command->leanpubInvoiceId());

            $this->purchaseRepository->save($purchase);

            $this->eventDispatcher->dispatchAll($purchase->releaseEvents());
        }
    }

    public function requestAccess(RequestAccess $command): void
    {
        $member = Member::requestAccess($command->leanpubInvoiceId(), $command->emailAddress());

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function claimPurchase(LeanpubInvoiceId $invoiceId): void
    {
        try {
            $purchase = $this->purchaseRepository->getById($invoiceId);
        } catch (CouldNotFindPurchase $exception) {
            $this->eventDispatcher->dispatch(new ClaimWasDenied($invoiceId, 'invalid_purchase_id'));
            return;
        }

        $purchase->claim();

        $this->purchaseRepository->save($purchase);

        $this->eventDispatcher->dispatchAll($purchase->releaseEvents());
    }

    public function grantAccess(LeanpubInvoiceId $memberId): void
    {
        $member = $this->memberRepository->getById($memberId);

        $member->grantAccess();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    /**
     * @param string|LeanpubInvoiceId $memberId
     */
    public function generateAccessToken($memberId): void
    {
        if (!$memberId instanceof LeanpubInvoiceId) {
            $memberId = LeanpubInvoiceId::fromString($memberId);
        }

        $member = $this->memberRepository->getById($memberId);

        $member->generateAccessToken($this->accessTokenGenerator);

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function planSession(PlanSession $command): SessionId
    {
        $sessionId = $this->sessionRepository->nextIdentity();

        $session = Session::plan(
            $sessionId,
            $command->date(),
            $command->description(),
            $command->maximumNumberOfParticipants()
        );

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());

        return $session->sessionId();
    }

    public function listUpcomingSessions(LeanpubInvoiceId $memberId): array
    {
        return $this->listUpcomingSessions->upcomingSessions($this->clock->currentTime(), $memberId);
    }

    public function attendSession(AttendSession $command): void
    {
        $session = $this->sessionRepository->getById($command->sessionId());

        $session->attend($command->memberId());

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function refreshBookInformation(): void
    {
        $bookSummary = $this->getBookSummary->getBookSummary();
        $this->assetPublisher->publishTitlePageImageUrl($bookSummary->titlePageUrl());
    }
}
