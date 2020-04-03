<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use Assert\Assert;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Purchase\CouldNotFindPurchase;
use LeanpubBookClub\Domain\Model\Purchase\Purchase;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class PurchaseTalisOrmRepository implements PurchaseRepository
{
    /**
     * @var AggregateRepository
     */
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Purchase $purchase): void
    {
        $this->aggregateRepository->save($purchase);
    }

    public function getById(LeanpubInvoiceId $invoiceId): Purchase
    {
        try {
            $purchase = $this->aggregateRepository->getById(
                Purchase::class,
                $invoiceId
            );
            Assert::that($purchase)->isInstanceOf(Purchase::class);
            /** @var Purchase $purchase */

            return $purchase;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindPurchase::withInvoiceId($invoiceId);
        }
    }
}
