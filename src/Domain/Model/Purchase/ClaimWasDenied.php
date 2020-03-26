<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class ClaimWasDenied
{
    private MemberId $memberId;

    private LeanpubInvoiceId $invoiceId;

    private string $reason;

    public function __construct(MemberId $memberId, LeanpubInvoiceId $invoiceId, string $reason)
    {
        $this->memberId = $memberId;
        $this->invoiceId = $invoiceId;
        $this->reason = $reason;
    }
}
