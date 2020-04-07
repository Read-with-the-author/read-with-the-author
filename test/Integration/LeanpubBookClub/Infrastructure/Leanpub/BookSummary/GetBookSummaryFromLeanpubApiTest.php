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
            ApiKey::fromString(Env::get('LEANPUB_API_KEY'))
        );

        $bookSummary = $getBookSummaryFromLeanpubApi->getBookSummary(
            BookSlug::fromString('microservices-for-everyone')
        );

        self::assertEquals(
            new BookSummary(
                'Microservices for everyone',
                '',
                'Matthias Noback',
                'https://d2sofvawe08yqg.cloudfront.net/microservices-for-everyone/original?1549495580',
                'http://leanpub.com/microservices-for-everyone',
                'http://leanpub.com/s/Tg286d6Q80d6JNiXfS1HBA.pdf',
                'http://leanpub.com/s/Tg286d6Q80d6JNiXfS1HBA.epub',
                'http://leanpub.com/s/Tg286d6Q80d6JNiXfS1HBA.mobi'
            ),
            $bookSummary
        );
    }
}
