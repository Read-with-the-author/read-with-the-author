<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\SessionCall;

use LeanpubBookClub\Domain\Model\Session\SessionId;
use RuntimeException;

final class CouldNotGetCallUrl extends RuntimeException
{
    public static function forSession(SessionId $sessionId): self
    {
        return new self(
            sprintf('Could not get call URL for session ' . $sessionId->asString())
        );
    }
}
