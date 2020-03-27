<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use PHPUnit\Framework\TestCase;
use LeanpubBookClub\Infrastructure\Env;

final class IndividualPurchaseFromLeanpubApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_loads_all_purchases_from_leanpub(): void
    {
        $individualPurchases = new IndividualPurchaseFromLeanpubApi(
            BookSlug::fromString('object-design'),
            ApiKey::fromString(Env::get('LEANPUB_API_KEY')),
            new PurchaseFactory()
        );

        $numberOfPurchases = 0;

        // TODO check that the most recent ones come first

        foreach ($individualPurchases->all() as $purchase) {
            self::assertInstanceOf(Purchase::class, $purchase);

            // Check that invoice ids have the format we expect them to have
            $purchase->invoiceId();

            $numberOfPurchases++;
        }

        self::assertGreaterThan(50, $numberOfPurchases, 'The API client is supposed to fetch more than one page');
    }
}
