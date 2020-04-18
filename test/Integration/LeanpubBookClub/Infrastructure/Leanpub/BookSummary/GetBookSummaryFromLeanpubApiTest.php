<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BaseUrl;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use PHPUnit\Framework\TestCase;
use LeanpubBookClub\Infrastructure\Env;

/**
 * @group slow
 */
final class GetBookSummaryFromLeanpubApiTest extends TestCase
{
    /**
     * @test
     */
    public function it_loads_a_book_summary_from_leanpub(): void
    {
        $getBookSummaryFromLeanpubApi = new GetBookSummaryFromLeanpubApi(
            ApiKey::fromString(Env::get('LEANPUB_API_KEY')),
            BaseUrl::fromString(Env::get('LEANPUB_API_BASE_URL'))
        );

        $bookSummary = $getBookSummaryFromLeanpubApi->getBookSummary(
            BookSlug::fromString(Env::get('LEANPUB_BOOK_SLUG'))
        );

        self::assertEquals(
            new BookSummary(
                'Advanced Web Application Architecture',
                '',
                'Matthias Noback',
                'http://fake_leanpub_server/title_page.jpg',
                'http://leanpub.com/web-application-architecture',
                'http://leanpub.com/s/oxS20NHbGUnWb4GEJyHYcF.pdf',
                'http://leanpub.com/s/oxS20NHbGUnWb4GEJyHYcF.epub',
                'http://leanpub.com/s/oxS20NHbGUnWb4GEJyHYcF.mobi'
            ),
            $bookSummary
        );
    }
}
