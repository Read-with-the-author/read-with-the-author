<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotAttendSession extends AbstractUserFacingError
{
    public static function becauseItHasBeenCancelled(SessionId $sessionId): self
    {
        return new self(
            'could_not_attend_session.because_it_has_been_cancelled',
            [
                'sessionId' => $sessionId->asString()
            ]
        );
    }
}
