<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\LeanpubSales;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class LeanpubSalesInMemory implements LeanpubSales
{
    /**
     * @var array<LeanpubInvoiceId>
     */
    private array $invoiceIdsOfActualPurchases;

    public function isInvoiceIdOfActualPurchase(LeanpubInvoiceId $leanpubInvoiceId): bool
    {
        foreach ($this->invoiceIdsOfActualPurchases as $actualInvoiceId) {
            if ($actualInvoiceId->equals($leanpubInvoiceId)) {
                return true;
            }
        }

        return false;
    }

    public function invoiceIdIsOfAnActualPurchase(LeanpubInvoiceId $leanpubInvoiceId): void
    {
        $this->invoiceIdsOfActualPurchases[] = $leanpubInvoiceId;
    }
}
