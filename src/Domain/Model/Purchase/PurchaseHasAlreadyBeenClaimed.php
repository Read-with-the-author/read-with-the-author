<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class PurchaseHasAlreadyBeenClaimed
{
    private LeanpubInvoiceId $leanpubInvoiceId;

    private MemberId $previouslyClaimedBy;

    public function __construct(LeanpubInvoiceId $leanpubInvoiceId, MemberId $previouslyClaimedBy)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
        $this->previouslyClaimedBy = $previouslyClaimedBy;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }
}
