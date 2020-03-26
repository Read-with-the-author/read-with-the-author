<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use function Safe\json_encode;

final class PurchaseFactory
{
    /**
     * @param array<string,mixed> $purchaseData
     * @return Purchase
     */
    public function createFromJsonDecodedData(array $purchaseData): Purchase
    {
        return new Purchase(
            self::extractValue($purchaseData, 'invoice_id'),
            self::extractValue($purchaseData, 'date_purchased')
        );
    }

    /**
     * @param array<string,mixed> $purchaseData
     */
    private static function extractValue(array $purchaseData, string $key): string
    {
        if (!isset($purchaseData[$key]) || !is_string($purchaseData[$key])) {
            throw CouldNotLoadIndividualPurchases::becauseInvoiceIdIsMissing(json_encode($purchaseData), $key);
        }

        return $purchaseData[$key];
    }
}
