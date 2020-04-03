<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use Assert\Assert;
use TalisOrm\AggregateId;

final class LeanpubInvoiceId implements AggregateId
{
    private string $leanpubInvoiceId;

    private function __construct(string $leanpubInvoiceId)
    {
        Assert::that($leanpubInvoiceId)->regex('/^[A-Za-z0-9_\-]{22}$/');
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->leanpubInvoiceId;
    }

    public function equals(LeanpubInvoiceId $other): bool
    {
        return $this->leanpubInvoiceId === $other->leanpubInvoiceId;
    }

    public function __toString(): string
    {
        return $this->asString();
    }
}
