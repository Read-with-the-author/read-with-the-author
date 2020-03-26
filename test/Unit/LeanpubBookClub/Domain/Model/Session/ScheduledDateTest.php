<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

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
