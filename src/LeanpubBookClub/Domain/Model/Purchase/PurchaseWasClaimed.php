<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class PurchaseWasClaimed
{
    private LeanpubInvoiceId $leanpubInvoiceId;

    private MemberId $claimedBy;

    public function __construct(LeanpubInvoiceId $leanpubInvoiceId, MemberId $claimedBy)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
        $this->claimedBy = $claimedBy;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }

    public function claimedBy(): MemberId
    {
        return $this->claimedBy;
    }
}
