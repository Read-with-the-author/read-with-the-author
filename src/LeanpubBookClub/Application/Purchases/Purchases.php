<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Purchases;

interface Purchases
{
    /**
     * @return array<Purchase>
     */
    public function listAllPurchases(): array;
}
