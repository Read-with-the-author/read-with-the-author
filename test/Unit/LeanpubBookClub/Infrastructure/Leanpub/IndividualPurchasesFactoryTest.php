<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use PHPUnit\Framework\TestCase;
use function Safe\file_get_contents;

final class IndividualPurchasesFactoryTest extends TestCase
{
    private IndividualPurchasesFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new IndividualPurchasesFactory();
    }

    /**
     * @test
     */
    public function it_fails_if_the_provided_json_is_invalid(): void
    {
        $this->expectException(CouldNotCreatePurchaseDtos::class);
        $this->expectExceptionMessage('JSON data is invalid');

        $this->factory->createFromJsonData('{"invalid"');
    }

    /**
     * @test
     */
    public function it_fails_if_the_provided_json_does_not_contain_an_array(): void
    {
        $this->expectException(CouldNotCreatePurchaseDtos::class);
        $this->expectExceptionMessage('JSON data does not have the expected structure');

        $this->factory->createFromJsonData('"not an array"');
    }

    /**
     * @test
     */
    public function it_fails_if_the_provided_json_does_not_contain_an_array_of_arrays(): void
    {
        $this->expectException(CouldNotCreatePurchaseDtos::class);
        $this->expectExceptionMessage('JSON data does not have the expected structure');

        $this->factory->createFromJsonData('["not an array"]');
    }

    /**
     * @test
     */
    public function it_fails_if_purchase_data_does_not_contain_an_invoice_id_key(): void
    {
        $this->expectException(CouldNotCreatePurchaseDtos::class);
        $this->expectExceptionMessage('the key invoice_id is missing');

        $this->factory->createFromJsonData('[{"date_purchased":""}]');
    }

    /**
     * @test
     */
    public function it_fails_if_purchase_data_does_not_contain_an_date_purchased_key(): void
    {
        $this->expectException(CouldNotCreatePurchaseDtos::class);
        $this->expectExceptionMessage('the key date_purchased is missing');

        $this->factory->createFromJsonData('[{"invoice_id":""}]');
    }

    /**
     * @test
     */
    public function it_converts_api_response_data_to_purchase_dtos(): void
    {
        $factory = new IndividualPurchasesFactory();

        $purchases = $factory->createFromJsonData(file_get_contents(__DIR__ . '/individual-purchases.json'));

        self::assertEquals(
            [
                new Purchase('jP6LfQ3UkfOvZTLZLNfDfg', '2019-04-15T06:36:54.000Z'),
                new Purchase('6gbXPEDMOEMKCNwOykPvpg', '2019-04-15T03:06:11.000Z')
            ],
            $purchases
        );
    }
}
