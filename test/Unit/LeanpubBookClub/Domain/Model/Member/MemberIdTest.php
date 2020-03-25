<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MemberIdTest extends TestCase
{
    /**
     * @test
     */
    public function it_requires_a_valid_uuid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MemberId::fromString('invalid');
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_a_string(): void
    {
        self::assertEquals(
            'd3ab365c-b594-4f49-8fd0-bb0bfa584703',
            MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703')->asString()
        );
    }

    /**
     * @test
     */
    public function it_can_be_compared_to_another_member_id(): void
    {
        self::assertTrue(
            MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703')->equals(
                MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703')
            )
        );

        self::assertFalse(
            MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703')->equals(
                MemberId::fromString('9aec8d8d-6cbc-4502-9583-06512e18ff86')
            )
        );
    }
}
