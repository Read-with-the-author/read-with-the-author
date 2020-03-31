<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use DateTimeZone;
use Exception;
use InvalidArgumentException;

final class TimeZone
{
    private string $timeZone;

    private function __construct(string $timeZone)
    {
        try {
            new DateTimeZone($timeZone);
        } catch (Exception $previous) {
            throw new InvalidArgumentException(sprintf('Invalid time zone "%s"', $timeZone), 0, $previous);
        }

        $this->timeZone = $timeZone;
    }

    public static function fromString(string $timeZone): self
    {
        return new self($timeZone);
    }

    public function asString(): string
    {
        return $this->timeZone;
    }

    public function asPhpDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone($this->timeZone);
    }
}
