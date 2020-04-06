<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;

final class Duration
{
    private int $minutes;

    private function __construct(int $minutes)
    {
        Assert::that($minutes)->greaterThan(0);
        $this->minutes = $minutes;
    }

    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes);
    }

    public function asInt(): int
    {
        return $this->minutes;
    }
}
