<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;

use LeanpubBookClub\Infrastructure\Mapping;

final class Purchase
{
    use Mapping;

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
            self::asString($purchaseData, 'invoice_id'),
            self::asString($purchaseData, 'date_purchased')
        );
    }
}
