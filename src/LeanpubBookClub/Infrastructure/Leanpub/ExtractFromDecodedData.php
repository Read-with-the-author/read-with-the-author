<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\CouldNotLoadIndividualPurchases;
use function Safe\json_encode;

trait ExtractFromDecodedData
{
    /**
     * @param array<string,mixed> $data
     */
    private static function extractString(array $data, string $key): string
    {
        if (!isset($data[$key]) || !is_string($data[$key])) {
            throw CouldNotLoadIndividualPurchases::becauseInvoiceIdIsMissing(json_encode($data), $key);
        }

        return $data[$key];
    }
}
