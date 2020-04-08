<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;

final class LeanpubInvoiceIdConstraint extends Constraint
{
    const INVALID_FORMAT_ERROR = 'b78de379-b68d-4fc2-b9f1-90367e7d81b3'; // I don't know what I'm doing

    public $message = 'leanpub_invoice_id.invalid';

    protected static $errorNames = [
        self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR'
    ];
}
