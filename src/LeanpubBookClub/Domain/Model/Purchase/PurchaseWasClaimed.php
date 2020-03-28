<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class PurchaseWasClaimed
{
    private LeanpubInvoiceId $leanpubInvoiceId;

    public function __construct(LeanpubInvoiceId $leanpubInvoiceId)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }
}
