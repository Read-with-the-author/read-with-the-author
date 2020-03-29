<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use PHPUnit\Framework\TestCase;

final class EmailAddressTest extends TestCase
{
    /**
     * @test
     */
    public function it_requires_the_provided_string_to_look_like_a_valid_email_address(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EmailAddress::fromString('does-not-look-good');
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_a_string(): void
    {
        self::assertEquals(
            'info@matthiasnoback.nl',
            EmailAddress::fromString('info@matthiasnoback.nl')->asString()
        );
    }
}
