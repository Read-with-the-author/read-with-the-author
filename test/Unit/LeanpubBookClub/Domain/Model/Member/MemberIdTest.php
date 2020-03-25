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
}
