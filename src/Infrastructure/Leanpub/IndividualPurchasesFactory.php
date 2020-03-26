<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Safe\Exceptions\JsonException;
use function Safe\json_decode;

final class IndividualPurchasesFactory
{
    /**
     * @return array<Purchase>
     */
    public function createFromJsonData(string $jsonData): array
    {
        try {
            $decodedData = json_decode($jsonData, true);
        } catch (JsonException $previous) {
            throw CouldNotCreatePurchaseDtos::becauseJsonDataIsInvalid($jsonData, $previous);
        }

        if (!is_array($decodedData)) {
            throw CouldNotCreatePurchaseDtos::becauseJsonDataStructureIsInvalid($jsonData);
        }

        $purchases = [];

        foreach ($decodedData as $purchaseData) {
            if (!is_array($purchaseData)) {
                throw CouldNotCreatePurchaseDtos::becauseJsonDataStructureIsInvalid($jsonData);
            }

            $purchases[] = new Purchase(
                self::extractValue($purchaseData, 'invoice_id'),
                self::extractValue($purchaseData, 'date_purchased')
            );
        }

        return $purchases;
    }

    private static function extractValue(array $purchaseData, string $key): string
    {
        if (!isset($purchaseData[$key]) || !is_string($purchaseData[$key])) {
            throw CouldNotCreatePurchaseDtos::becauseInvoiceIdIsMissing($purchaseData, $key);
        }

        return $purchaseData[$key];
    }
}
