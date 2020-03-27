<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface LeanpubSales
{
    public function isInvoiceIdOfActualPurchase(LeanpubInvoiceId $leanpubInvoiceId): bool;
}
