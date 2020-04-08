<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Validation;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ExistingLeanpubInvoiceIdConstraintValidator extends ConstraintValidator
{
    private MemberRepository $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistingLeanpubInvoiceIdConstraint) {
            throw new UnexpectedTypeException($constraint, ExistingLeanpubInvoiceIdConstraint::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $leanpubInvoiceId = LeanpubInvoiceId::fromString($value);
        } catch (InvalidArgumentException $exception) {
            // Ignore; let the format of the ID be validated with a LeanpubInvoiceIdConstraint
            return;
        }

        if (!$this->memberRepository->exists($leanpubInvoiceId)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(ExistingLeanpubInvoiceIdConstraint::DOES_NOT_EXIST_ERROR)
                ->addViolation();
        }
    }
}
