<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Purchases\Purchases;

final class PurchasesInMemory implements Purchases
{
    public function listAllPurchases(): array
    {
        return [];
    }
}
