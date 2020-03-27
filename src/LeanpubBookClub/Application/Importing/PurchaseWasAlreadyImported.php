<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Importing;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use RuntimeException;

final class PurchaseWasAlreadyImported extends RuntimeException
{
    public function __construct(LeanpubInvoiceId $leanpubInvoiceId)
    {
        parent::__construct(
            sprintf(
                'Could not import purchase with invoice ID %s because it was already imported',
                $leanpubInvoiceId->asString()
            )
        );
    }
}
