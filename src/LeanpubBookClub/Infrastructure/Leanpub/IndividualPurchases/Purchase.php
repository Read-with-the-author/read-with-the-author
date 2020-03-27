<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;

use LeanpubBookClub\Infrastructure\Leanpub\ExtractFromDecodedData;

final class Purchase
{
    use ExtractFromDecodedData;

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

    /**
     * @param array<string,mixed> $purchaseData
     * @return Purchase
     */
    public static function createFromJsonDecodedData(array $purchaseData): Purchase
    {
        return new Purchase(
            self::extractString($purchaseData, 'invoice_id'),
            self::extractString($purchaseData, 'date_purchased')
        );
    }
}
