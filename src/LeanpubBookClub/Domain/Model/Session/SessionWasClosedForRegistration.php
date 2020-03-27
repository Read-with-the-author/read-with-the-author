<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

final class SessionWasClosedForRegistration
{
    private SessionId $sessionId;

    public function __construct(SessionId $sessionId)
    {
        $this->sessionId = $sessionId;
    }
}
