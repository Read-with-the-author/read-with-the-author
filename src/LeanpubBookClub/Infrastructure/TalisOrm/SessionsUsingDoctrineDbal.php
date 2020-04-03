<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use DateTimeImmutable;
use LeanpubBookClub\Application\UpcomingSessions\CouldNotFindSession;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Infrastructure\Doctrine\Connection;

final class SessionsUsingDoctrineDbal implements Sessions
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function upcomingSessions(DateTimeImmutable $currentTime, LeanpubInvoiceId $activeMemberId): array
    {
        return [];
    }

    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array
    {
        return [];
    }

    public function getSessionForAdministrator(SessionId $sessionId): SessionForAdministrator
    {
        throw CouldNotFindSession::withId($sessionId);
    }
}
