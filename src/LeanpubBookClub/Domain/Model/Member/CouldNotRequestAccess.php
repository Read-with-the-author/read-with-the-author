<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use RuntimeException;

final class CouldNotRequestAccess extends RuntimeException
{
    public static function becauseInvoiceIdHasBeenUsedBefore(LeanpubInvoiceId $leanpubInvoiceId): self
    {
        return new self(
            sprintf(
                'Could not request access because invoice ID "%s" has been used before',
                $leanpubInvoiceId->asString()
            )
        );
    }
}
