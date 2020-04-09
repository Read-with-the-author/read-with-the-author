<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Generator;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseFactoryMethods;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Infrastructure\IntegrationTestServiceContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group repositories
 */
final class PurchaseRepositoryContractTest extends KernelTestCase
{
    use PurchaseFactoryMethods;

    /**
     * @test
     * @dataProvider purchases
     */
    public function it_can_save_and_get_a_purchase_by_its_id(Purchase $purchase): void
    {
        $this->purchaseRepository()->save($purchase);

        self::assertEquals($purchase, $this->purchaseRepository()->getById($purchase->leanpubInvoiceId()));
    }

    /**
     * @test
     * @dataProvider purchases
     */
    public function it_can_save_a_purchase_correctly_between_fetches_it_from_the_repository(
        Purchase $purchase,
        callable $updateFunction
    ): void {
        $this->purchaseRepository()->save($purchase);

        $purchase = $this->purchaseRepository()->getById($purchase->leanpubInvoiceId());
        $updateFunction($purchase);

        $this->purchaseRepository()->save($purchase);

        self::assertEquals($purchase, $this->purchaseRepository()->getById($purchase->leanpubInvoiceId()));
    }

    /**
     * @return Generator<array<int,Purchase|\Closure>>
     */
    public function purchases(): Generator
    {
        yield [
            Purchase::import($this->aRandomLeanpubInvoiceId()),
            function (Purchase $purchase): void {
                // change nothing
            }
        ];

        yield [
            Purchase::import($this->aRandomLeanpubInvoiceId()),
            function (Purchase $purchase): void {
                $purchase->claim();
            }
        ];
    }

    protected function tearDown(): void
    {
        $this->doctrineDbal()->executeQuery('DELETE FROM ' . Purchase::tableName() . ' WHERE 1');
    }

    private function purchaseRepository(): PurchaseRepository
    {
        if (self::$container === null) {
            self::bootKernel();
        }

        $serviceContainer = self::$container->get(IntegrationTestServiceContainer::class);
        Assert::that($serviceContainer)->isInstanceOf(IntegrationTestServiceContainer::class);
        /** @var IntegrationTestServiceContainer $serviceContainer */

        return $serviceContainer->purchaseRepository();
    }

    private function doctrineDbal(): Connection
    {
        if (self::$container === null) {
            self::bootKernel();
        }

        $connection = self::$container->get(Connection::class);
        Assert::that($connection)->isInstanceOf(Connection::class);
        /** @var Connection $connection */

        return $connection;
    }
}
