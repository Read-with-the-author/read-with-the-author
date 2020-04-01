<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface ListUpcomingSessions
{
    /**
     * @return array<UpcomingSession>
     */
    public function upcomingSessions(DateTimeImmutable $currentTime, LeanpubInvoiceId $activeMemberId): array;

    /**
     * @param DateTimeImmutable $currentTime
     * @return array<UpcomingSessionForAdministrator>
     */
    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array;
}
