<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use PHPUnit\Framework\TestCase;

final class IndividualPurchasesFactoryTest extends TestCase
{
    private PurchaseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new PurchaseFactory();
    }

    /**
     * @test
     */
    public function it_fails_if_purchase_data_does_not_contain_an_invoice_id_key(): void
    {
        $this->expectException(CouldNotLoadIndividualPurchases::class);
        $this->expectExceptionMessage('the key invoice_id is missing');

        $this->factory->createFromJsonDecodedData(['date_purchased' => '']);
    }

    /**
     * @test
     */
    public function it_fails_if_purchase_data_does_not_contain_an_date_purchased_key(): void
    {
        $this->expectException(CouldNotLoadIndividualPurchases::class);
        $this->expectExceptionMessage('the key date_purchased is missing');

        $this->factory->createFromJsonDecodedData(['invoice_id' => '']);
    }
}
