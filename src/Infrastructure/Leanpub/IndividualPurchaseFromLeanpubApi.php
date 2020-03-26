<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Assert\Assert;
use Generator;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use GuzzleHttp\Psr7\Request;
use Safe\Exceptions\JsonException;
use function Safe\json_decode;
use function Safe\json_encode;

final class IndividualPurchaseFromLeanpubApi implements IndividualPurchases
{
    private string $bookSlug;

    private string $apiKey;

    private PurchaseFactory $individualPurchasesFactory;

    public function __construct(string $bookSlug, string $apiKey, PurchaseFactory $individualPurchasesFactory)
    {
        Assert::that($bookSlug)->notEmpty('Leanpub book slug should not be empty');
        Assert::that($apiKey)->notEmpty('Leanpub API key should not be empty');

        $this->bookSlug = $bookSlug;
        $this->apiKey = $apiKey;
        $this->individualPurchasesFactory = $individualPurchasesFactory;
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

                yield $this->individualPurchasesFactory->createFromJsonDecodedData($purchaseData);
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
                    $this->bookSlug,
                    $this->apiKey,
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
