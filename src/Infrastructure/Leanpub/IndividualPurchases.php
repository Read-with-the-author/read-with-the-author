<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Generator;

interface IndividualPurchases
{
    /**
     * @return Generator<Purchase>
     */
    public function all(): Generator;
}
