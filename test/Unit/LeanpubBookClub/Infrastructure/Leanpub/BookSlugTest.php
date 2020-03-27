<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BookSlugTest extends TestCase
{
    /**
     * @test
     */
    public function a_book_slug_should_be_a_non_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BookSlug::fromString('');
    }

    /**
     * @test
     */
    public function it_can_be_converted_back_to_a_string(): void
    {
        self::assertEquals(
            'book-slug',
            BookSlug::fromString('book-slug')->asString()
        );
    }
}
