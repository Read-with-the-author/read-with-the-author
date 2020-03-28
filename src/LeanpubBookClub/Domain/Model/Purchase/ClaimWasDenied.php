<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class ClaimWasDenied
{
    private LeanpubInvoiceId $invoiceId;

    private string $reason;

    public function __construct(LeanpubInvoiceId $leanpubInvoiceId, string $reason)
    {
        $this->invoiceId = $leanpubInvoiceId;
        $this->reason = $reason;
    }
}
