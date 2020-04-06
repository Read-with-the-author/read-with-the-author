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
use LeanpubBookClub\Infrastructure\Mapping;

final class SessionsUsingDoctrineDbal implements Sessions
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function upcomingSessions(DateTimeImmutable $currentTime, LeanpubInvoiceId $activeMemberId): array
    {
        // @todo only upcoming sessions

        $rows = $this->connection->selectAll(
            $this->createQueryBuilderForMember($activeMemberId)
        );

        return array_map([$this, 'createSessionForMember'], $rows);
    }

    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array
    {
        $rows = $this->connection->selectAll(
            $this->createQueryBuilderForAdministrator()
                ->orderBy('date', 'ASC')
        );

        // @todo only upcoming sessions

        return array_map([$this, 'createSessionForAdministrator'], $rows);
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
        $row = $this->connection->selectOne(
            $this->createQueryBuilderForMember($memberId)
                ->andWhere('sessionId = :sessionId')
                ->setParameter('sessionId', $sessionId->asString())
        );

        return $this->createSessionForMember($row);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function createSessionForAdministrator(array $row): SessionForAdministrator
    {
        $session = new SessionForAdministrator(
            self::asString($row, 'sessionId'),
            self::asString($row, 'date'),
            self::asInt($row, 'duration'),
            self::asString($row, 'description'),
            self::asInt($row, 'maximumNumberOfAttendees')
        );

        return $session
            ->withUrlForCall(self::asStringOrNull($row, 'urlForCall'))
            ->withNumberOfAttendees(self::asInt($row, 'numberOfAttendees'));
    }

    /**
     * @param array<string,mixed> $row
     */
    private function createSessionForMember(array $row): SessionForMember
    {
        $session = new SessionForMember(
            self::asString($row, 'sessionId'),
            self::asString($row, 'date'),
            self::asString($row, 'description')
        );

        return $session
            ->withUrlForCall(self::asStringOrNull($row, 'urlForCall'))
            ->withActiveMemberRegisteredAsAttendee(self::asInt($row, 'memberIsRegisteredAsAttendee') > 0);
    }

    private function createQueryBuilderForAdministrator(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->addSelect('(SELECT COUNT(*) FROM attendees a WHERE a.sessionId = sessionId) AS numberOfAttendees')
            ->from('sessions');
    }

    private function createQueryBuilderForMember(LeanpubInvoiceId $memberId): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->addSelect(
                '(SELECT COUNT(*) FROM attendees WHERE attendees.sessionId = sessions.sessionId AND attendees.memberId = :memberId) AS memberIsRegisteredAsAttendee'
            )
            ->setParameter('memberId', $memberId->asString())
            ->from('sessions');
    }
}
