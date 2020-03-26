<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;
use LeanpubBookClub\Domain\Model\Common\Entity;

final class Session
{
    use Entity;

    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    private function __construct(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description
    ) {
        Assert::that($description)->notEmpty('The session description should not be empty');

        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
    }

    public static function plan(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description
    ): self {
        $session = new self($sessionId, $date, $description);

        $session->events[] = new SessionWasPlanned($sessionId, $date, $description);

        return $session;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }
}
