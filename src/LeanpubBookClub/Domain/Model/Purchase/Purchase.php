<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use TalisOrm\Aggregate;
use TalisOrm\AggregateBehavior;
use TalisOrm\AggregateId;
use TalisOrm\Schema\SpecifiesSchema;

final class Purchase implements Aggregate, SpecifiesSchema
{
    use AggregateBehavior;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private bool $wasClaimed = false;

    private function __construct()
    {
    }

    public static function import(LeanpubInvoiceId $leanpubInvoiceId): self
    {
        $purchase = new self();
        $purchase->leanpubInvoiceId = $leanpubInvoiceId;

        $purchase->events[] = new PurchaseImported($leanpubInvoiceId);

        return $purchase;
    }

    public function claim(): void
    {
        if ($this->wasClaimed) {
            $this->events[] = new PurchaseHasAlreadyBeenClaimed($this->leanpubInvoiceId);
            return;
        }

        $this->wasClaimed = true;

        $this->events[] = new PurchaseWasClaimed($this->leanpubInvoiceId);
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }

    public static function specifySchema(Schema $schema): void
    {
        $table = $schema->createTable(self::tableName());

        $table->addColumn('leanpubInvoiceId', 'string');
        $table->setPrimaryKey(['leanpubInvoiceId']);

        $table->addColumn('wasClaimed', 'boolean')->setNotnull(true);
    }

    /**
     * @template T
     * @return array<class-string<T>,array<T>>
     */
    public function childEntitiesByType(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public static function childEntityTypes(): array
    {
        return [];
    }

    /**
     * @template T
     * @param array<string,string|int|float|bool|null> $aggregateState
     * @param array<class-string<T>, array<T>> $childEntitiesByType
     * @return self
     */
    public static function fromState(array $aggregateState, array $childEntitiesByType): self
    {
        $instance = new self();

        $instance->leanpubInvoiceId = LeanpubInvoiceId::fromString((string)$aggregateState['leanpubInvoiceId']);
        $instance->wasClaimed = (bool)$aggregateState['wasClaimed'];

        return $instance;
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function state(): array
    {
        return [
            'leanpubInvoiceId' => $this->leanpubInvoiceId->asString(),
            'wasClaimed' => $this->wasClaimed
        ];
    }

    public static function tableName(): string
    {
        return 'purchases';
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public function identifier(): array
    {
        return [
            'leanpubInvoiceId' => $this->leanpubInvoiceId->asString()
        ];
    }

    /**
     * @return array<string,string|int|float|bool|null>
     */
    public static function identifierForQuery(AggregateId $aggregateId): array
    {
        return [
            'leanpubInvoiceId' => (string)$aggregateId
        ];
    }
}
