<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use LeanpubBookClub\Application\Purchases\Purchase;
use LeanpubBookClub\Application\Purchases\Purchases;
use LeanpubBookClub\Infrastructure\Doctrine\Connection;
use LeanpubBookClub\Infrastructure\Mapping;

final class PurchasesUsingDoctrineDbal implements Purchases
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function listAllPurchases(): array
    {
        $records = $this->connection->selectAll(
            $this->connection->createQueryBuilder()
                ->from('purchases')
                ->select('*')
        );

        return array_map(
            function (array $record): Purchase {
                return new Purchase(
                    self::asString($record, 'leanpubInvoiceId'),
                    self::asBool($record, 'wasClaimed')
                );
            },
            $records
        );
    }
}
