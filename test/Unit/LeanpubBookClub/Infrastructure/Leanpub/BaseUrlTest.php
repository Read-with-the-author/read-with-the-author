<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BaseUrlTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidUrls
     */
    public function it_requires_a_valid_base_url_starting_with_http_or_https(string $invalidUrl): void
    {
        $this->expectException(InvalidArgumentException::class);

        BaseUrl::fromString($invalidUrl);
    }

    /**
     * @return array<array<string>>
     */
    public function invalidUrls(): array
    {
        return [
            ['htt://test'],
            ['httpsq://test'],
            ['http://'],
            ['https://'],
        ];
    }

    /**
     * @test
     */
    public function it_strips_the_trailing_slash(): void
    {
        self::assertEquals(
            'https://leanpub.com',
            BaseUrl::fromString('https://leanpub.com/')->asString()
        );
    }
}
