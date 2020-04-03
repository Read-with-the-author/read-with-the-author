<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Doctrine;

use Assert\Assert;
use Doctrine\DBAL\Connection as DbalConnection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PDO;

final class Connection
{
    private DbalConnection $dbalConnection;

    public function __construct(DbalConnection $dbalConnection)
    {
        $this->dbalConnection = $dbalConnection;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->dbalConnection->createQueryBuilder();
    }

    /**
     * @return array<string,mixed> The query result as an associative array
     */
    public function selectOne(QueryBuilder $queryBuilder): array
    {
        $data = $this->execute($queryBuilder)->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw NoResult::forQuery($queryBuilder->getSQL(), $queryBuilder->getParameters());
        }

        return $data;
    }

    /**
     * @return array<array<string,mixed>> The query result as an associative array
     */
    public function selectAll(QueryBuilder $queryBuilder): array
    {
        return $this->execute($queryBuilder)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return Statement<array>
     */
    private function execute(QueryBuilder $queryBuilder): Statement
    {
        $statement = $queryBuilder->execute();
        Assert::that($statement)->isInstanceOf(Statement::class);

        return $statement;
    }
}
