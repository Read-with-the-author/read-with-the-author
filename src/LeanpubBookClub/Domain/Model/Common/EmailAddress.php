<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use Assert\Assert;

final class EmailAddress
{
    private string $emailAddress;

    private function __construct(string $emailAddress)
    {
        Assert::that($emailAddress)->email();

        $this->emailAddress = $emailAddress;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->emailAddress;
    }
}
