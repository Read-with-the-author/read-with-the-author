<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use Doctrine\DBAL\Schema\Schema;
use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use TalisOrm\Schema\SpecifiesSchema;

final class Purchase implements SpecifiesSchema
{
    use Entity;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private bool $wasClaimed = false;

    private function __construct(LeanpubInvoiceId $leanpubInvoiceId)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public static function import(LeanpubInvoiceId $leanpubInvoiceId): self
    {
        $purchase = new self($leanpubInvoiceId);

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
        $table = $schema->createTable('purchases');

        $table->addColumn('leanpubInvoiceId', 'string');
        $table->setPrimaryKey(['leanpubInvoiceId']);

        $table->addColumn('wasClaimed', 'boolean')->setNotnull(true);
    }
}
