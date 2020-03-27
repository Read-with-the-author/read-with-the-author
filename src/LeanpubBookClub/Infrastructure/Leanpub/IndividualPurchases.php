<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Generator;

interface IndividualPurchases
{
    /**
     * Returns all individual purchases, most recent purchases first
     *
     * @return Generator<Purchase>
     */
    public function all(): Generator;
}
