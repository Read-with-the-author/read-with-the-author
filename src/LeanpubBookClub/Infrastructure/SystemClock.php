<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use DateTimeImmutable;
use DateTimeZone;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class SystemClock implements Clock
{
    private DateTimeZone $authorTimeZone;

    public function __construct(TimeZone $authorTimeZone)
    {
        $this->authorTimeZone = $authorTimeZone->asPhpDateTimeZone();
    }

    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->authorTimeZone);
    }
}
