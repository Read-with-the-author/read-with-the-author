<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use LeanpubBookClub\Domain\Model\Session\SessionId;
use RuntimeException;

final class CouldNotFindSession extends RuntimeException
{
    public static function withId(SessionId $sessionId): self
    {
        return new self(
            sprintf('Could not find session with ID ' . $sessionId->asString())
        );
    }
}
