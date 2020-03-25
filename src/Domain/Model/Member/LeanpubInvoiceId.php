<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use Assert\Assert;

final class LeanpubInvoiceId
{
    private string $leanpubInvoiceId;

    private function __construct(string $leanpubInvoiceId)
    {
        Assert::that($leanpubInvoiceId)->regex('/^[A-Za-z0-9]{22}$/');
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
}
