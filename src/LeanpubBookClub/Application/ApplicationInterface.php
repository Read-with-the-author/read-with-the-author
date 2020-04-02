<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\SessionCall\SetCallUrl;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSessionForAdministrator;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface ApplicationInterface extends Members
{
    public function importAllPurchases(): void;

    public function importPurchase(ImportPurchase $command): void;

    public function requestAccess(RequestAccess $command): void;

    public function claimPurchase(LeanpubInvoiceId $invoiceId): void;

    public function grantAccess(LeanpubInvoiceId $memberId): void;

    public function planSession(PlanSession $command): string;

    public function updateSession(UpdateSession $updateSession): void;

    /**
     * @return array<UpcomingSession> & UpcomingSession[]
     */
    public function listUpcomingSessions(LeanpubInvoiceId $memberId): array;

    /**
     * @return array<UpcomingSessionForAdministrator> & UpcomingSessionForAdministrator[]
     */
    public function listUpcomingSessionsForAdministrator(): array;

    public function attendSession(AttendSession $command): void;

    public function cancelAttendance(CancelAttendance $command): void;

    public function refreshBookInformation(): void;

    /**
     * @param string|LeanpubInvoiceId $memberId
     */
    public function generateAccessToken($memberId): void;

    public function updateTimeZone(UpdateTimeZone $command): void;

    public function setCallUrl(SetCallUrl $command): void;

    /**
     * @throws CouldNotGetCallUrl
     */
    public function getCallUrlForSession(string $sessionId): string;

    public function getSessionForAdministrator(string $sessionId): UpcomingSessionForAdministrator;
}
