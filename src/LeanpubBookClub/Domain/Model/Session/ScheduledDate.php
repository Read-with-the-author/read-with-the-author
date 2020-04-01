<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Throwable;

final class ScheduledDate
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i';

    private string $dateTime;

    private function __construct(string $dateTime, string $timeZone)
    {
        try {
            $dateTimeImmutable = DateTimeImmutable::createFromFormat(
                self::DATE_TIME_FORMAT,
                $dateTime,
                new DateTimeZone($timeZone));
            if ($dateTimeImmutable === false) {
                throw new InvalidArgumentException('The provided date/time string did not match the expected format');
            }
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date/time format. Provided: %s, expected format: %s',
                    $dateTime,
                    self::DATE_TIME_FORMAT
                ),
                0,
                $throwable
            );
        }

        $this->dateTime = $dateTimeImmutable
            ->setTimezone(new DateTimeZone('UTC'))
            ->format(self::DATE_TIME_FORMAT);
    }

    public static function fromString(string $dateTime, string $timeZone = 'UTC'): ScheduledDate
    {
        return new self($dateTime, $timeZone);
    }

    public static function fromDateTime(DateTimeImmutable $dateTime): ScheduledDate
    {
        $dateTimeAsString = $dateTime
            ->setTimezone(new DateTimeZone('UTC'))
            ->format(self::DATE_TIME_FORMAT);

        return new self($dateTimeAsString, 'UTC');
    }

    public function asString(): string
    {
        return $this->dateTime;
    }

    public function asPhpDateTime(): DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat(
            self::DATE_TIME_FORMAT,
            $this->dateTime,
            new DateTimeZone('UTC')
        );

        Assert::that($dateTime)->isInstanceOf(DateTimeImmutable::class);

        return $dateTime;
    }
}
