<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class ImportPurchase
{
    /**
     * @var string
     */
    private string $leanpubInvoiceId;

    public function __construct(string $leanpubInvoiceId)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString($this->leanpubInvoiceId);
    }
}
