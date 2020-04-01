<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\SessionId;

interface ApplicationInterface extends Members
{
    public function importAllPurchases(): void;

    public function importPurchase(ImportPurchase $command): void;

    public function requestAccess(RequestAccess $command): void;

    public function claimPurchase(LeanpubInvoiceId $invoiceId): void;

    public function grantAccess(LeanpubInvoiceId $memberId): void;

    public function planSession(PlanSession $command): SessionId;

    /**
     * @return array<UpcomingSession> & UpcomingSession[]
     */
    public function listUpcomingSessions(LeanpubInvoiceId $memberId): array;

    public function attendSession(AttendSession $command): void;

    public function cancelAttendance(CancelAttendance $command): void;

    public function refreshBookInformation(): void;

    /**
     * @param string|LeanpubInvoiceId $memberId
     */
    public function generateAccessToken($memberId): void;

    public function updateTimeZone(UpdateTimeZone $command): void;
}
