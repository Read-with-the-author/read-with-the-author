<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Assert\Assert;

final class BookSlug
{
    private string $bookSlug;

    private function __construct(string $bookSlug)
    {
        Assert::that($bookSlug)->notEmpty('Leanpub book slug should not be empty');
        $this->bookSlug = $bookSlug;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->bookSlug;
    }
}
