<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\SessionId;

interface Sessions
{
    /**
     * @return array<UpcomingSession>
     */
    public function upcomingSessions(DateTimeImmutable $currentTime, LeanpubInvoiceId $activeMemberId): array;

    /**
     * @param DateTimeImmutable $currentTime
     * @return array<SessionForAdministrator>
     */
    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array;

    /**
     * @throws CouldNotFindSession
     */
    public function getSessionForAdministrator(SessionId $sessionId): SessionForAdministrator;
}
