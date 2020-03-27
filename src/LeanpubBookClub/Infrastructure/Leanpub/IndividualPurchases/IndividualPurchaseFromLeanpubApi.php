<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;

use Generator;
use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use Safe\Exceptions\JsonException;
use function Safe\json_decode;
use function Safe\json_encode;

final class IndividualPurchaseFromLeanpubApi implements IndividualPurchases
{
    private BookSlug $bookSlug;

    private ApiKey $apiKey;

    public function __construct(BookSlug $bookSlug, ApiKey $apiKey)
    {
        $this->bookSlug = $bookSlug;
        $this->apiKey = $apiKey;
    }

    public function all(): Generator
    {
        $adapter = GuzzleAdapter::createWithConfig(
            [
                'timeout' => 10
            ]
        );

        $page = 1;

        while (true) {
            $decodedData = $this->loadPage($adapter, $page);

            if (isset($decodedData['data']) && count($decodedData['data']) === 0) {
                // We know we reached the last page when the response contains an empty "data" key
                break;
            }

            foreach ($decodedData as $purchaseData) {
                if (!is_array($purchaseData)) {
                    throw CouldNotLoadIndividualPurchases::becauseJsonDataStructureIsInvalid(json_encode($purchaseData));
                }

                yield Purchase::createFromJsonDecodedData($purchaseData);
            }

            $page++;
        }
    }

    /**
     * @return array<mixed>
     */
    private function loadPage(GuzzleAdapter $adapter, int $page): array
    {
        $response = $adapter->sendRequest(
            new Request(
                'GET',
                sprintf(
                    'https://leanpub.com/%s/individual_purchases.json?api_key=%s&page=%d',
                    $this->bookSlug->asString(),
                    $this->apiKey->asString(),
                    $page
                )
            )
        );

        $jsonData = $response->getBody()->getContents();

        try {
            $decodedData = json_decode($jsonData, true);
        } catch (JsonException $previous) {
            throw CouldNotLoadIndividualPurchases::becauseJsonDataIsInvalid($jsonData, $previous);
        }

        if (!is_array($decodedData)) {
            throw CouldNotLoadIndividualPurchases::becauseJsonDataStructureIsInvalid($jsonData);
        }

        return $decodedData;
    }
}
