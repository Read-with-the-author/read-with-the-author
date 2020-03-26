<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Common\EntityTestCase;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberId;

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
                new PurchaseImported($leanpubInvoiceId)
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

        $purchase->claim($this->aMember());

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
        $purchase->claim($this->aMember());
        $purchase->releaseEvents();

        $purchase->claim($this->anotherMember());

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

    private function aMember(): MemberId
    {
        return MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703');
    }

    private function anotherMember(): MemberId
    {
        return MemberId::fromString('9aec8d8d-6cbc-4502-9583-06512e18ff86');
    }
}
