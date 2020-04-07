<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Infrastructure\Leanpub\ApiKey;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;

final class Configuration
{
    private string $leanpubBookSlug;

    private string $leanpubApiKey;

    private string $systemEmailAddress;

    private string $authorTimeZone;

    public function __construct(
        string $leanpubBookSlug,
        string $leanpubApiKey,
        string $systemEmailAddress,
        string $authorTimeZone
    ) {
        $this->leanpubBookSlug = $leanpubBookSlug;
        $this->leanpubApiKey = $leanpubApiKey;
        $this->systemEmailAddress = $systemEmailAddress;
        $this->authorTimeZone = $authorTimeZone;
    }

    public function leanpubBookSlug(): BookSlug
    {
        return BookSlug::fromString($this->leanpubBookSlug);
    }

    public function leanpubApiKey(): ApiKey
    {
        return ApiKey::fromString($this->leanpubApiKey);
    }

    public function systemEmailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->systemEmailAddress);
    }

    public function authorTimeZone(): TimeZone
    {
        return TimeZone::fromString($this->authorTimeZone);
    }
}
