<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;

use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BaseUrl;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use PHPUnit\Framework\TestCase;
use LeanpubBookClub\Infrastructure\Env;

/**
 * @group slow
 */
final class IndividualPurchaseFromLeanpubApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_loads_all_purchases_from_leanpub(): void
    {
        $individualPurchases = new IndividualPurchaseFromLeanpubApi(
            BookSlug::fromString(Env::get('LEANPUB_BOOK_SLUG')),
            ApiKey::fromString(Env::get('LEANPUB_API_KEY')),
            BaseUrl::fromString(Env::get('LEANPUB_API_BASE_URL'))
        );

        $numberOfPurchases = 0;

        $lastPurchaseDate = null;

        foreach ($individualPurchases->all() as $purchase) {
            self::assertInstanceOf(Purchase::class, $purchase);
            /** @var Purchase $purchase */

            // Check that invoice ids have the format we expect them to have
            $purchase->invoiceId();

            if ($lastPurchaseDate !== null) {
                // Check that the purchases are sorted by purchase date descending
                self::assertTrue(strcmp($lastPurchaseDate, $purchase->datePurchased()) >= 0);
            }

            $lastPurchaseDate = $purchase->datePurchased();

            $numberOfPurchases++;
        }

        self::assertEquals(3, $numberOfPurchases, 'The API client is supposed to fetch more than one page');
    }
}
