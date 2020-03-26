<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface PurchaseRepository
{
    public function save(Purchase $purchase): void;

    /**
     * @throws CouldNotFindPurchase
     */
    public function getById(LeanpubInvoiceId $invoiceId): Purchase;
}
