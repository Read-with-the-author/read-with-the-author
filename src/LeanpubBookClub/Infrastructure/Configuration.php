<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;

final class Configuration
{
    private string $leanpubBookSlug;

    private string $leanpubApiKey;

    private string $projectDirectory;

    public function __construct(string $leanpubBookSlug, string $leanpubApiKey, string $projectDirectory)
    {
        Assert::that($projectDirectory)->directory();

        $this->leanpubBookSlug = $leanpubBookSlug;
        $this->leanpubApiKey = $leanpubApiKey;
        $this->projectDirectory = $projectDirectory;
    }

    public static function createFromEnvironmentVariables(string $projectDirectory): self
    {
        return new self(
            Env::get('LEANPUB_BOOK_SLUG'),
            Env::get('LEANPUB_API_KEY'),
            $projectDirectory
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

    public function assetsDirectory(): string
    {
        return $this->projectDirectory . '/public/assets';
    }
}
