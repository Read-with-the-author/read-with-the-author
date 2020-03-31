<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Assert\Assert;
use DateTimeImmutable;
use DateTimeZone;
use LeanpubBookClub\Application\Clock;

final class FakeClock implements Clock
{
    private ?DateTimeImmutable $currentTime = null;

    private DateTimeZone $authorTimeZone;

    public function __construct(DateTimeZone $authorTimeZone)
    {
        $this->authorTimeZone = $authorTimeZone;
    }

    public function currentTime(): DateTimeImmutable
    {
        Assert::that($this->currentTime)->isInstanceOf(DateTimeImmutable::class);

        return $this->currentTime;
    }

    public function setCurrentTime(string $time): void
    {
        $this->setCurrentTimeFromFormattedString('Y-m-d H:i', $time);
    }

    public function setCurrentDate(string $date): void
    {
        $this->setCurrentTimeFromFormattedString('Y-m-d', $date);
    }

    private function setCurrentTimeFromFormattedString(string $format, string $time): void
    {
        $currentTime = DateTimeImmutable::createFromFormat($format, $time, $this->authorTimeZone);
        Assert::that($currentTime)->isInstanceOf(DateTimeImmutable::class);

        $this->currentTime = $currentTime;
    }
}
