<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\CouldNotLoadIndividualPurchases;
use Safe\Exceptions\JsonException;
use function Safe\json_decode;

final class GetBookSummaryFromLeanpubApi implements GetBookSummary
{
    private BookSlug $bookSlug;

    private ApiKey $apiKey;

    public function __construct(BookSlug $bookSlug, ApiKey $apiKey)
    {
        $this->bookSlug = $bookSlug;
        $this->apiKey = $apiKey;
    }

    public function getBookSummary(): BookSummary
    {
        $adapter = GuzzleAdapter::createWithConfig(
            [
                'timeout' => 10
            ]
        );

        $response = $adapter->sendRequest(
            new Request(
                'GET',
                sprintf(
                    'https://leanpub.com/%s.json?api_key=%s',
                    $this->bookSlug->asString(),
                    $this->apiKey->asString()
                )
            )
        );

        $jsonData = $response->getBody()->getContents();

        try {
            $decodedData = json_decode($jsonData, true);
        } catch (JsonException $previous) {
            throw CouldNotLoadIndividualPurchases::becauseJsonDataIsInvalid($jsonData, $previous);
        }

        return BookSummary::fromJsonDecodedData($decodedData);
    }
}
