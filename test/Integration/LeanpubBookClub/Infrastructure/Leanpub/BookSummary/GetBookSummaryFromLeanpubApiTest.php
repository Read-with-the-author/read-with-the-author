<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use PHPUnit\Framework\TestCase;
use LeanpubBookClub\Infrastructure\Env;

final class GetBookSummaryFromLeanpubApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_loads_a_book_summary_from_leanpub(): void
    {
        $getBookSummaryFromLeanpubApi = new GetBookSummaryFromLeanpubApi(
            BookSlug::fromString('object-design'),
            ApiKey::fromString(Env::get('LEANPUB_API_KEY'))
        );

        $bookSummary = $getBookSummaryFromLeanpubApi->getBookSummary();

        self::assertEquals(
            new BookSummary(
                'Style Guide for Object Design',
                '',
                'Matthias Noback',
                'https://d2sofvawe08yqg.cloudfront.net/object-design/original?1552991369',
                'http://leanpub.com/object-design'
            ),
            $bookSummary
        );
    }
}
