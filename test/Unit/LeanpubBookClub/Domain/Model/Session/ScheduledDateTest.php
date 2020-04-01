<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ScheduledDateTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidDates
     */
    public function it_does_not_accept_an_invalid_date(string $invalidDate): void
    {
        $this->expectException(InvalidArgumentException::class);

        $date = ScheduledDate::fromString($invalidDate);
        echo $date->asString();
    }

    /**
     * @test
     */
    public function its_time_zone_will_be_set_to_utc_by_default(): void
    {
        $date = ScheduledDate::fromString('2020-04-01 10:00');
        self::assertEquals(new DateTimeZone('UTC'), $date->asPhpDateTime()->getTimezone());
    }

    /**
     * @test
     */
    public function it_can_be_created_from_a_string_with_a_different_time_zone(): void
    {
        $date = ScheduledDate::fromString('2020-04-01 10:00', 'Europe/Amsterdam');
        self::assertEquals('2020-04-01 08:00', $date->asString());
    }

    /**
     * @test
     */
    public function when_provided_with_a_DateTimeImmutable_object_it_will_set_the_timezone_to_UTC_first(): void
    {
        $dateTime = new DateTimeImmutable('2020-04-01 10:00', new DateTimeZone('Europe/Amsterdam'));
        $date = ScheduledDate::fromDateTime($dateTime);

        self::assertEquals('2020-04-01 08:00', $date->asString());
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function invalidDates(): array
    {
        return [
            'date format does not match expected format' => ['2020-2 10:00']
        ];
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_a_string(): void
    {
        self::assertEquals(
            '2020-02-01 10:11',
            ScheduledDate::fromString('2020-02-01 10:11')->asString()
        );
    }
}
