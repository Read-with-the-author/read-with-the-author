<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class Purchase
{
    use Entity;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private bool $wasClaimed = false;

    private function __construct(LeanpubInvoiceId $leanpubInvoiceId)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public static function import(LeanpubInvoiceId $leanpubInvoiceId): self
    {
        $purchase = new self($leanpubInvoiceId);

        $purchase->events[] = new PurchaseImported($leanpubInvoiceId);

        return $purchase;
    }

    public function claim(): void
    {
        if ($this->wasClaimed) {
            $this->events[] = new PurchaseHasAlreadyBeenClaimed($this->leanpubInvoiceId);
            return;
        }

        $this->wasClaimed = true;

        $this->events[] = new PurchaseWasClaimed($this->leanpubInvoiceId);
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }
}
