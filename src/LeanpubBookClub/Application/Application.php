<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\Importing\PurchaseWasAlreadyImported;
use LeanpubBookClub\Application\Members\Member as MemberReadModel;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\Purchases\Purchases;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\SessionCall\SetCallUrl;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceIdHasBeenUsedBefore;
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
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;

final class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private PurchaseRepository $purchaseRepository;

    private SessionRepository $sessionRepository;

    private Clock $clock;

    private Sessions $sessions;

    private IndividualPurchases $individualPurchases;

    private AccessTokenGenerator $accessTokenGenerator;

    private Members $members;
    private Purchases $purchases;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        PurchaseRepository $purchaseRepository,
        SessionRepository $sessionRepository,
        Clock $clock,
        Sessions $sessions,
        IndividualPurchases $individualPurchases,
        AccessTokenGenerator $accessTokenGenerator,
        Members $members,
        Purchases $purchases
    ) {
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchaseRepository = $purchaseRepository;
        $this->sessionRepository = $sessionRepository;
        $this->clock = $clock;
        $this->sessions = $sessions;
        $this->individualPurchases = $individualPurchases;
        $this->accessTokenGenerator = $accessTokenGenerator;
        $this->members = $members;
        $this->purchases = $purchases;
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
        if ($this->memberRepository->exists($command->leanpubInvoiceId())) {
            throw LeanpubInvoiceIdHasBeenUsedBefore::id($command->leanpubInvoiceId());
        }

        $member = Member::requestAccess(
            $command->leanpubInvoiceId(),
            $command->emailAddress(),
            $command->timeZone(),
            $this->clock->currentTime()
        );

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

    public function clearAccessToken($memberId): void
    {
        if (!$memberId instanceof LeanpubInvoiceId) {
            $memberId = LeanpubInvoiceId::fromString($memberId);
        }

        $member = $this->memberRepository->getById($memberId);

        $member->clearAccessToken();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function planSession(PlanSession $command): string
    {
        $sessionId = $this->sessionRepository->nextIdentity();

        $session = Session::plan(
            $sessionId,
            $command->date(),
            $command->duration(),
            $command->description(),
            $command->maximumNumberOfParticipants()
        );

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());

        return $session->sessionId()->asString();
    }

    public function updateSession(UpdateSession $command): void
    {
        $session = $this->sessionRepository->getById($command->sessionId());

        $session->update(
            $command->description(),
            $command->urlForCall()
        );

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function cancelSession(string $sessionId): void
    {
        $session = $this->sessionRepository->getById(SessionId::fromString($sessionId));

        $session->cancel();

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function setCallUrl(SetCallUrl $command): void
    {
        $session = $this->sessionRepository->getById($command->sessionId());

        $session->setCallUrl($command->callUrl());

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function listUpcomingSessionsForMember(string $memberId): array
    {
        return $this->sessions->upcomingSessions(
            $this->clock->currentTime(),
            LeanpubInvoiceId::fromString($memberId)
        );
    }

    public function listUpcomingSessionsForAdministrator(): array
    {
        return $this->sessions->upcomingSessionsForAdministrator($this->clock->currentTime());
    }

    public function attendSession(AttendSession $command): void
    {
        $session = $this->sessionRepository->getById($command->sessionId());

        $session->attend($command->memberId());

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function cancelAttendance(CancelAttendance $command): void
    {
        $session = $this->sessionRepository->getById($command->sessionId());

        $session->cancelAttendance($command->memberId());

        $this->sessionRepository->save($session);

        $this->eventDispatcher->dispatchAll($session->releaseEvents());
    }

    public function getOneMemberByAccessToken(string $accessToken): MemberReadModel
    {
        return $this->members->getOneByAccessToken(AccessToken::fromString($accessToken));
    }

    public function getOneMemberById(string $memberId): MemberReadModel
    {
        return $this->members->getOneById(LeanpubInvoiceId::fromString($memberId));
    }

    public function updateTimeZone(UpdateTimeZone $command): void
    {
        $member = $this->memberRepository->getById($command->memberId());

        $member->changeTimeZone($command->timeZone());

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function getCallUrlForSession(string $sessionId, string $memberId): string
    {
        $memberId = LeanpubInvoiceId::fromString($memberId);

        $sessionId = SessionId::fromString($sessionId);
        $session = $this->sessions->getSessionForMember(
            $sessionId,
            $memberId
        );

        if (!$session->memberIsRegisteredAsAttendee()) {
            throw CouldNotGetCallUrl::becauseMemberIsNotARegisteredAttendee($sessionId, $memberId);
        }

        if (!is_string($session->urlForCall())) {
            throw CouldNotGetCallUrl::becauseItHasNotBeenDeterminedYet($sessionId);
        }

        return $session->urlForCall();
    }

    public function getSessionForAdministrator(string $sessionId): SessionForAdministrator
    {
        return $this->sessions->getSessionForAdministrator(SessionId::fromString($sessionId));
    }

    public function listMembersForAdministrator(): array
    {
        return $this->members->listMembers();
    }

    public function listAllPurchasesForAdministrator(): array
    {
        return $this->purchases->listAllPurchases();
    }
}
