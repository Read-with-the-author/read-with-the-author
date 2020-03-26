<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Member\MemberId;

final class AttendeeRegisteredForSession
{
    private SessionId $sessionId;

    private MemberId $memberId;

    public function __construct(SessionId $sessionId, MemberId $memberId)
    {
        $this->sessionId = $sessionId;
        $this->memberId = $memberId;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function memberId(): MemberId
    {
        return $this->memberId;
    }
}
