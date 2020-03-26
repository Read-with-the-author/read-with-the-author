<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class Purchase
{
    use Entity;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private ?MemberId $claimedByMember = null;

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

    public function claim(MemberId $claimedBy): void
    {
        if ($this->claimedByMember instanceof MemberId) {
            $this->events[] = new PurchaseHasAlreadyBeenClaimed($this->leanpubInvoiceId, $this->claimedByMember);
            return;
        }

        $this->claimedByMember = $claimedBy;

        $this->events[] = new PurchaseWasClaimed($this->leanpubInvoiceId, $this->claimedByMember);
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }
}
