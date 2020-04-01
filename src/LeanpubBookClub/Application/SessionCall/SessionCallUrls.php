<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\SessionCall;

use LeanpubBookClub\Domain\Model\Session\SessionId;

interface SessionCallUrls
{
    /**
     * @throw CouldNotGetCallUrlForSession
     */
    public function getCallUrlForSession(SessionId $sessionId): string;
}
