<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LeanpubInvoiceIdTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidInvoiceIds
     */
    public function it_requires_a_valid_looking_leanpub_invoice_id(string $invalidInvoiceId): void
    {
        $this->expectException(InvalidArgumentException::class);
        LeanpubInvoiceId::fromString($invalidInvoiceId);
    }

    /**
     * @return array<string,array<int,string>>
     */
    public function invalidInvoiceIds(): array
    {
        return [
            'too short' => ['jP6LfQ3UkfOvZTLZLNfDf'],
            'too long' => ['jP6LfQ3UkfOvZTLZLNfDfgh'],
            'unexpected characters' => ['*jP6LfQ3UkfOvZTLZLNfD^']
        ];
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_string(): void
    {
        self::assertEquals(
            'jP6LfQ3UkfOvZTLZLNfDfg',
            LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg')->asString()
        );
    }

    /**
     * @test
     */
    public function it_can_be_compared_to_another_id(): void
    {
        self::assertTrue(
            LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg')->equals(
                LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg')
            )
        );
        self::assertFalse(
            LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg')->equals(
                LeanpubInvoiceId::fromString('6gbXPEDMOEMKCNwOykPvpg')
            )
        );
    }
}
