<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Common\EntityTestCase;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class PurchaseTest extends EntityTestCase
{
    /**
     * @test
     */
    public function it_can_be_created_based_on_an_invoice_id_from_leanpub(): void
    {
        $leanpubInvoiceId = $this->anInvoiceId();
        $purchase = Purchase::import($leanpubInvoiceId);

        self::assertEquals(
            [
                new PurchaseWasImported($leanpubInvoiceId)
            ],
            $purchase->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function it_can_be_claimed(): void
    {
        $purchase = $this->aPurchase();

        $purchase->claim();

        self::assertArrayContainsObjectOfType(
            PurchaseWasClaimed::class,
            $purchase->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function it_can_not_be_claimed_twice(): void
    {
        $purchase = $this->aPurchase();
        $purchase->claim();
        $purchase->releaseEvents();

        $purchase->claim();

        self::assertArrayContainsObjectOfType(
            PurchaseHasAlreadyBeenClaimed::class,
            $purchase->releaseEvents()
        );
    }

    private function anInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg');
    }

    private function aPurchase(): Purchase
    {
        return Purchase::import($this->anInvoiceId());
    }
}
