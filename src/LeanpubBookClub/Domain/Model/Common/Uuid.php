<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use Assert\Assert;

trait Uuid
{
    private string $id;

    private function __construct(string $id)
    {
        Assert::that($id)->uuid();

        $this->id = $id;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->id;
    }
}
