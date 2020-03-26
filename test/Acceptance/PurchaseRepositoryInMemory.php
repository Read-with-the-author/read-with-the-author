<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Purchase\CouldNotFindPurchase;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;

final class PurchaseRepositoryInMemory implements PurchaseRepository
{
    /**
     * @var array<string,Purchase>
     */
    private array $purchases = [];

    public function save(Purchase $purchase): void
    {
        $this->purchases[$purchase->leanpubInvoiceId()->asString()] = $purchase;
    }

    public function getById(LeanpubInvoiceId $invoiceId): Purchase
    {
        if (!isset($this->purchases[$invoiceId->asString()])) {
            throw CouldNotFindPurchase::withInvoiceId($invoiceId);
        }

        return $this->purchases[$invoiceId->asString()];
    }
}
