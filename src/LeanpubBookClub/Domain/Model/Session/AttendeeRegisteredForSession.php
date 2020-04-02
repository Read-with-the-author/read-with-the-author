<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class AttendeeRegisteredForSession
{
    private SessionId $sessionId;

    private LeanpubInvoiceId $memberId;

    private ScheduledDate $date;

    public function __construct(SessionId $sessionId, LeanpubInvoiceId $memberId, ScheduledDate $date)
    {
        $this->sessionId = $sessionId;
        $this->memberId = $memberId;
        $this->date = $date;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }
}
