<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;

final class UniqueLeanpubInvoiceIdConstraint extends Constraint
{
    const ALREADY_USED_ERROR = '675e76e3-1034-4bcc-aeb2-689352759df7'; // I don't know what I'm doing

    public $message = 'leanpub_invoice_id.already_used';

    protected static $errorNames = [
        self::ALREADY_USED_ERROR => 'ALREADY_USED_ERROR',
    ];
}
