<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\Members\MemberForAdministrator;
use LeanpubBookClub\Application\Purchases\Purchase;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\SessionCall\SetCallUrl;
use LeanpubBookClub\Application\UpcomingSessions\SessionForMember;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface ApplicationInterface
{
    public function importAllPurchases(): void;

    public function importPurchase(ImportPurchase $command): void;

    public function requestAccess(RequestAccess $command): void;

    public function claimPurchase(LeanpubInvoiceId $invoiceId): void;

    public function grantAccess(LeanpubInvoiceId $memberId): void;

    public function planSession(PlanSession $command): string;

    public function updateSession(UpdateSession $updateSession): void;

    public function cancelSession(string $sessionId): void;

    /**
     * @return array<SessionForMember> & SessionForMember[]
     */
    public function listUpcomingSessionsForMember(string $memberId): array;

    /**
     * @return array<SessionForAdministrator> & SessionForAdministrator[]
     */
    public function listUpcomingSessionsForAdministrator(): array;

    public function attendSession(AttendSession $command): void;

    public function cancelAttendance(CancelAttendance $command): void;

    /**
     * @param string|LeanpubInvoiceId $memberId
     */
    public function generateAccessToken($memberId): void;

    /**
     * @param string|LeanpubInvoiceId $memberId
     */
    public function clearAccessToken($memberId): void;

    public function updateTimeZone(UpdateTimeZone $command): void;

    public function setCallUrl(SetCallUrl $command): void;

    /**
     * @throws CouldNotGetCallUrl
     */
    public function getCallUrlForSession(string $sessionId, string $memberId): string;

    public function getSessionForAdministrator(string $sessionId): SessionForAdministrator;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByAccessToken(string $accessToken): Member;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberById(string $memberId): Member;

    /**
     * @return array<MemberForAdministrator>
     */
    public function listMembersForAdministrator(): array;

    /**
     * @return array<Purchase>
     */
    public function listAllPurchasesForAdministrator(): array;
}
