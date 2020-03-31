<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TimeZoneTest extends TestCase
{
    /**
     * @test
     */
    public function it_requires_a_valid_time_zone_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TimeZone::fromString('invalid');
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_a_string(): void
    {
        self::assertEquals(
            'Europe/Amsterdam',
            TimeZone::fromString('Europe/Amsterdam')->asString()
        );
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_a_PHP_DateTimeZone_object(): void
    {
        self::assertEquals(
            new DateTimeZone('Europe/Amsterdam'),
            TimeZone::fromString('Europe/Amsterdam')->asPhpDateTimeZone()
        );
    }
}
