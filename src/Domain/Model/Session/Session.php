<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;
use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class Session
{
    use Entity;

    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    private int $maximumNumberOfParticipantsAllowed;

    private function __construct(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description,
        int $maximumNumberOfParticipantsAllowed
    ) {
        Assert::that($description)->notEmpty('The session description should not be empty');
        Assert::that($maximumNumberOfParticipantsAllowed)
            ->greaterThan(0, 'The maximum number of participants should be greater than 0');

        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
        $this->maximumNumberOfParticipantsAllowed = $maximumNumberOfParticipantsAllowed;
    }

    public static function plan(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description,
        int $maximumNumberOfParticipantsAllowed
    ): self {
        $session = new self($sessionId, $date, $description, $maximumNumberOfParticipantsAllowed);

        $session->events[] = new SessionWasPlanned($sessionId, $date, $description);

        return $session;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function attend(MemberId $memberId): void
    {
        $this->events[] = new AttendeeRegisteredForSession($this->sessionId, $memberId);
    }
}
