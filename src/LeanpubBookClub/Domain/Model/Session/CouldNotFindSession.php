<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use RuntimeException;

final class CouldNotFindSession extends RuntimeException
{
    public static function withId(SessionId $sessionId): self
    {
        return new self(
            'Could not find session with ID ' . $sessionId->asString()
        );
    }
}
