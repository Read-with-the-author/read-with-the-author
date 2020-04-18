<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Assert\Assert;

final class BaseUrl
{
    private string $baseUrl;

    private function __construct(string $baseUrl)
    {
        Assert::that($baseUrl)->regex('#^http(s)?://.+#');
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->baseUrl;
    }
}
