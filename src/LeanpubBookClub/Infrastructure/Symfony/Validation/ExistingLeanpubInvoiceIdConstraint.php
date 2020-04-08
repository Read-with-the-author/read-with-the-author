<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;

final class ExistingLeanpubInvoiceIdConstraint extends Constraint
{
    const DOES_NOT_EXIST_ERROR = '76bdc08e-22d7-45d5-9157-7281ee55000d'; // I don't know what I'm doing

    public $message = 'leanpub_invoice_id.does_not_exist';

    protected static $errorNames = [
        self::DOES_NOT_EXIST_ERROR => 'DOES_NOT_EXIST_ERROR',
    ];
}
