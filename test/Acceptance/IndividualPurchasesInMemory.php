<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;
use LeanpubBookClub\Infrastructure\Leanpub\Purchase;

final class IndividualPurchasesInMemory implements IndividualPurchases
{
    /**
     * @var array<Purchase>
     */
    private $purchases = [];

    public function add(Purchase $purchase): void
    {
        $this->purchases[] = $purchase;
    }

    public function all(): array
    {
        return $this->purchases;
    }
}
