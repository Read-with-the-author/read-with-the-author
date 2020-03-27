<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;

final class Configuration
{
    private string $leanpubBookSlug;

    private string $leanpubApiKey;

    public function __construct(string $leanpubBookSlug, string $leanpubApiKey)
    {
        $this->leanpubBookSlug = $leanpubBookSlug;
        $this->leanpubApiKey = $leanpubApiKey;
    }

    public static function createFromEnvironmentVariables(): self
    {
        return new self(
            Env::get('LEANPUB_BOOK_SLUG'),
            Env::get('LEANPUB_API_KEY')
        );
    }

    public function leanpubBookSlug(): BookSlug
    {
        return BookSlug::fromString($this->leanpubBookSlug);
    }

    public function leanpubApiKey(): ApiKey
    {
        return ApiKey::fromString($this->leanpubApiKey);
    }
}
