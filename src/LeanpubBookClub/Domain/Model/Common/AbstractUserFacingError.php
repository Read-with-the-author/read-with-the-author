<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use RuntimeException;

abstract class AbstractUserFacingError extends RuntimeException implements UserFacingError
{
    private string $translationId;

    /**
     * @var array<string,mixed>
     */
    private array $translationParameters;

    /**
     * @param array<string,mixed> $translationParameters
     */
    public function __construct(string $translationId, array $translationParameters = [])
    {
        parent::__construct($translationId);

        $this->translationId = $translationId;
        $this->translationParameters = $translationParameters;
    }

    public function translationId(): string
    {
        return $this->translationId;
    }

    /**
     * @return array<string,mixed>
     */
    public function translationParameters(): array
    {
        return $this->translationParameters;
    }
}
