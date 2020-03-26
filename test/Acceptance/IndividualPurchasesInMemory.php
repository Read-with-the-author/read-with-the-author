<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Generator;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;
use LeanpubBookClub\Infrastructure\Leanpub\Purchase;

final class IndividualPurchasesInMemory implements IndividualPurchases
{
    /**
     * @var array<Purchase>
     */
    private array $purchases = [];

    public function add(Purchase $purchase): void
    {
        $this->purchases[] = $purchase;
    }

    public function all(): Generator
    {
        foreach ($this->purchases as $purchase) {
            yield $purchase;
        }
    }
}
