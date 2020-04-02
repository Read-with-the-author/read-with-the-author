<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm\TalisOrmBundle\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProviderInterface;
use TalisOrm\Schema\AggregateSchemaProvider;

final class AggregateMigrationsSchemaProvider implements SchemaProviderInterface
{
    private AggregateSchemaProvider $aggregateSchemaProvider;

    public function __construct(AggregateSchemaProvider $aggregateSchemaProvider)
    {
        $this->aggregateSchemaProvider = $aggregateSchemaProvider;
    }

    public function createSchema(): Schema
    {
        return $this->aggregateSchemaProvider->createSchema();
    }
}
