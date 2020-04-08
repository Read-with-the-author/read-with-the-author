<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

use Throwable;

interface UserFacingError extends Throwable
{
    public function translationId(): string;

    /**
     * @return array<string,mixed>
     */
    public function translationParameters(): array;
}
