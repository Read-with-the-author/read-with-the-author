<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Validation;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class LeanpubInvoiceIdConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LeanpubInvoiceIdConstraint) {
            throw new UnexpectedTypeException($constraint, LeanpubInvoiceIdConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            LeanpubInvoiceId::fromString($value);
        } catch (InvalidArgumentException $exception) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(LeanpubInvoiceIdConstraint::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
