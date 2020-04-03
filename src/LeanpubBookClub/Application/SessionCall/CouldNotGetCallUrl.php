<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\SessionCall;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use RuntimeException;

final class CouldNotGetCallUrl extends RuntimeException
{
    public static function becauseItHasNotBeenDeterminedYet(SessionId $sessionId): self
    {
        return new self(
            sprintf(
                'Could not get call URL for session %s because it has not been determined yet',
                $sessionId->asString()
            )
        );
    }

    public static function becauseMemberIsNotARegisteredAttendee(SessionId $sessionId, LeanpubInvoiceId $memberId): self
    {
        return new self(
            sprintf(
                'Could not get call URL for session %s because this member is not registered as an attendee (%s)',
                $sessionId->asString(),
                $memberId->asString()
            )
        );
    }
}
