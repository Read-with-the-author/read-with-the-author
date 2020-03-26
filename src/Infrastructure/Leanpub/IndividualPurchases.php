<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

interface IndividualPurchases
{
    /**
     * @return array<Purchase>
     */
    public function all(): array;
}
