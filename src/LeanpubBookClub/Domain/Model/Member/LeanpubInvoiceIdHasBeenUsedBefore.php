<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\AbstractUserFacingError;

final class LeanpubInvoiceIdHasBeenUsedBefore extends AbstractUserFacingError
{
    public static function id(LeanpubInvoiceId $leanpubInvoiceId): self
    {
        return new self(
            'leanpub_invoice_id.already_used',
            [
                '{leanpubInvoiceId}' => $leanpubInvoiceId->asString()
            ]
        );
    }
}
