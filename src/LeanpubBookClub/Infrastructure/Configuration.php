<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use DateTimeZone;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;

final class Configuration
{
    private string $leanpubBookSlug;

    private string $leanpubApiKey;

    private string $projectDirectory;

    private string $systemEmailAddress;

    private string $authorTimeZone;

    public function __construct(
        string $leanpubBookSlug,
        string $leanpubApiKey,
        string $projectDirectory,
        string $systemEmailAddress,
        string $authorTimeZone
    ) {
        Assert::that($projectDirectory)->directory();

        $this->leanpubBookSlug = $leanpubBookSlug;
        $this->leanpubApiKey = $leanpubApiKey;
        $this->projectDirectory = $projectDirectory;
        $this->systemEmailAddress = $systemEmailAddress;
        $this->authorTimeZone = $authorTimeZone;
    }

    public static function createFromEnvironmentVariables(string $projectDirectory): self
    {
        return new self(
            Env::get('LEANPUB_BOOK_SLUG'),
            Env::get('LEANPUB_API_KEY'),
            $projectDirectory,
            Env::get('SYSTEM_EMAIL_ADDRESS'),
            Env::get('AUTHOR_TIMEZONE', 'CET')
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

    public function systemEmailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->systemEmailAddress);
    }

    public function authorTimeZone(): DateTimeZone
    {
        return new DateTimeZone($this->authorTimeZone);
    }
}
