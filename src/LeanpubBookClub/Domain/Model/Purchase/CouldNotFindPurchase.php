<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use RuntimeException;

final class CouldNotFindPurchase extends RuntimeException
{
    public static function withInvoiceId(LeanpubInvoiceId $invoiceId): self
    {
        return new self(
            sprintf(
                'Could not find Leanpub purchase with invoice ID %s',
                $invoiceId->asString()
            )
        );
    }
}
