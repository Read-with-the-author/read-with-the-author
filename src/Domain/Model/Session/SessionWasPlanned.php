<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

final class SessionWasPlanned
{
    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    public function __construct(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description
    ) {
        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
    }
}
