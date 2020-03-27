<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub;

use Assert\Assert;

final class ApiKey
{
    private string $apiKey;

    private function __construct(string $apiKey)
    {
        Assert::that($apiKey)->notEmpty('Leanpub API key should not be empty');
        $this->apiKey = $apiKey;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->apiKey;
    }
}
