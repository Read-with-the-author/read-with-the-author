<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

final class Purchase
{
    private string $invoiceId;

    private string $datePurchased;

    public function __construct(string $invoiceId, string $datePurchased)
    {
        $this->invoiceId = $invoiceId;
        $this->datePurchased = $datePurchased;
    }

    public function invoiceId(): string
    {
        return $this->invoiceId;
    }

    public function datePurchased(): string
    {
        return $this->datePurchased;
    }
}
