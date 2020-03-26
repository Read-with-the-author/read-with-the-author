<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Assert\Assert;
use DateTimeImmutable;
use LeanpubBookClub\Application\Clock;

final class FakeClock implements Clock
{
    private ?DateTimeImmutable $currentTime = null;

    public function currentTime(): DateTimeImmutable
    {
        Assert::that($this->currentTime)->isInstanceOf(DateTimeImmutable::class);

        return $this->currentTime;
    }

    public function setCurrentTime(DateTimeImmutable $currentTime): void
    {
        $this->currentTime = $currentTime;
    }
}
