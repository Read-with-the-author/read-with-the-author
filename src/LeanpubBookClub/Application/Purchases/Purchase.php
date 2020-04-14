<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Purchases;

final class Purchase
{
    private string $leanpubInvoiceId;
    private bool $wasClaimed;

    public function __construct(string $leanpubInvoiceId, bool $wasClaimed)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
        $this->wasClaimed = $wasClaimed;
    }

    public function leanpubInvoiceId(): string
    {
        return $this->leanpubInvoiceId;
    }

    public function wasClaimed(): bool
    {
        return $this->wasClaimed;
    }
}
