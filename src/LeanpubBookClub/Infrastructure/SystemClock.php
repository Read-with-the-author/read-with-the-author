<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use DateTimeImmutable;
use DateTimeZone;
use LeanpubBookClub\Application\Clock;

final class SystemClock implements Clock
{
    private DateTimeZone $authorTimeZone;

    public function __construct(DateTimeZone $authorTimeZone)
    {
        $this->authorTimeZone = $authorTimeZone;
    }

    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->authorTimeZone);
    }
}
