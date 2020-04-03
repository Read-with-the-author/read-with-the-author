<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use DateTimeImmutable;
use Doctrine\DBAL\Query\QueryBuilder;
use LeanpubBookClub\Application\UpcomingSessions\CouldNotFindSession;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use LeanpubBookClub\Application\UpcomingSessions\SessionForMember;
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
        $rows = $this->connection->selectAll(
            $this->createQueryBuilderForAdministrator()->orderBy('date', 'ASC')
        );

        // @todo only upcoming sessions

        return $this->createSessionsForAdministrator($rows);
    }

    public function getSessionForAdministrator(SessionId $sessionId): SessionForAdministrator
    {
        $row = $this->connection->selectOne(
            $this->createQueryBuilderForAdministrator()
                ->andWhere('sessionId = :sessionId')
                ->setParameter('sessionId', $sessionId->asString())
        );

        return $this->createSessionForAdministrator($row);
    }

    public function getSessionForMember(SessionId $sessionId, LeanpubInvoiceId $memberId): SessionForMember
    {
        throw CouldNotFindSession::withId($sessionId);
    }

    /**
     * @param array<array<string,mixed>> $rows
     * @return array<SessionForAdministrator>
     */
    private function createSessionsForAdministrator(array $rows): array
    {
        return array_map(
            [$this, 'createSessionForAdministrator'],
            $rows
        );
    }

    /**
     * @param array<string,mixed> $row
     * @return SessionForAdministrator
     */
    private function createSessionForAdministrator(array $row): SessionForAdministrator
    {
        return (new SessionForAdministrator(
            (string)$row['sessionId'],
            (string)$row['date'],
            (string)$row['description'],
            (int)$row['maximumNumberOfAttendees']
        ))->withUrlForCall($row['urlForCall'])->withNumberOfAttendees((int)$row['numberOfAttendees']);
    }

    private function createQueryBuilderForAdministrator(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->addSelect('(SELECT COUNT(*) FROM attendees a WHERE a.sessionId = sessionId) AS numberOfAttendees')
            ->from('sessions');
    }
}
