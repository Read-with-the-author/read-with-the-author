<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\Members\MemberForAdministrator;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Infrastructure\Doctrine\Connection;
use LeanpubBookClub\Infrastructure\Doctrine\NoResult;
use LeanpubBookClub\Infrastructure\Mapping;

final class MembersUsingDoctrineDbal implements Members
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOneByAccessToken(AccessToken $accessToken): Member
    {
        try {
            $data = $this->connection->selectOne(
                $this->connection->createQueryBuilder()
                    ->select('*')
                    ->from('members')
                    ->andWhere('accessToken = :accessToken')
                    ->setParameter('accessToken', $accessToken->asString())
            );

            return $this->createMember($data);
        } catch (NoResult $exception) {
            throw CouldNotFindMember::withAccessToken($accessToken);
        }
    }

    public function getOneById(LeanpubInvoiceId $memberId): Member
    {
        try {
            $data = $this->connection->selectOne(
                $this->connection->createQueryBuilder()
                    ->select('*')
                    ->from('members')
                    ->andWhere('memberId = :memberId')
                    ->setParameter('memberId', $memberId->asString())
            );

            return $this->createMember($data);
        } catch (NoResult $exception) {
            throw CouldNotFindMember::withId($memberId);
        }
    }

    public function listMembers(): array
    {
        $records = $this->connection->selectAll(
            $this->connection->createQueryBuilder()
            ->select('*')
            ->from('members')
            ->orderBy('wasGrantedAccess', 'desc')
        );

        return array_map(function (array $record): MemberForAdministrator {
            return new MemberForAdministrator(
                self::asString($record, 'memberId'),
                self::asString($record, 'emailAddress'),
                self::asString($record, 'requestedAccessAt'),
                self::asBool($record, 'wasGrantedAccess')
            );
        }, $records);
    }

    /**
     * @param array<string,mixed> $data
     * @return Member
     */
    private function createMember($data): Member
    {
        return new Member(
            self::asString($data, 'memberId'),
            self::asString($data, 'timeZone'),
            self::asString($data, 'emailAddress')
        );
    }
}
