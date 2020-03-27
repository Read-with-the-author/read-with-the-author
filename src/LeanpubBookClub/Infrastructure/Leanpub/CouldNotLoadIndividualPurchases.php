<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use RuntimeException;
use Safe\Exceptions\JsonException;
use function Safe\json_encode;

final class CouldNotLoadIndividualPurchases extends RuntimeException
{
    public static function becauseJsonDataIsInvalid(string $jsonData, JsonException $previous): self
    {
        return new self(
            sprintf(
                'Could not create Purchase DTOs because the provided JSON data is invalid: %s',
                $jsonData
            ),
            0,
            $previous
        );
    }

    public static function becauseJsonDataStructureIsInvalid(string $jsonData): self
    {
        return new self(
            sprintf(
                'Could not create Purchase DTOs because the provided JSON data does not have the expected structure: %s',
                $jsonData
            )
        );
    }

    public static function becauseInvoiceIdIsMissing(string $purchaseData, string $expectedKey): self
    {
        return new self(
            sprintf(
                'Could not create Purchase DTOs because the key %s is missing: %s',
                $expectedKey,
                $purchaseData
            )
        );
    }
}
