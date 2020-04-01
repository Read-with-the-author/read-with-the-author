<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class Attendee
{
    private SessionId $sessionId;

    private LeanpubInvoiceId $memberId;

    public function __construct(SessionId $sessionId, LeanpubInvoiceId $memberId)
    {
        $this->sessionId = $sessionId;
        $this->memberId = $memberId;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }
}
