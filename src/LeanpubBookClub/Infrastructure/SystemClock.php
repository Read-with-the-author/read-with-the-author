<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use DateTimeImmutable;
use DateTimeZone;
use LeanpubBookClub\Application\Clock;

final class SystemClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
