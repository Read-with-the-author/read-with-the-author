<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use Assert\Assert;
use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class Session
{
    use Entity;

    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    private int $maximumNumberOfParticipantsAllowed;

    /**
     * @var array<LeanpubInvoiceId> & LeanpubInvoiceId[]
     */
    private array $attendees = [];

    private bool $wasClosed = false;

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

    public function attend(LeanpubInvoiceId $memberId): void
    {
        if ($this->wasClosed) {
            // When the session was closed, we don't accept new attendees
            return;
        }

        foreach ($this->attendees as $attendee) {
            if ($attendee->equals($memberId)) {
                // No need to register the same attendee again
                return;
            }
        }

        $this->attendees[] = $memberId;

        $this->events[] = new AttendeeRegisteredForSession($this->sessionId, $memberId);

        if (count($this->attendees) >= $this->maximumNumberOfParticipantsAllowed) {
            $this->close();
        }
    }

    private function close(): void
    {
        $this->wasClosed = true;

        $this->events[] = new SessionWasClosedForRegistration($this->sessionId);
    }

    public function cancelAttendance(LeanpubInvoiceId $memberId): void
    {
        foreach ($this->attendees as $key => $attendee) {
            if ($attendee->equals($memberId)) {
                unset($this->attendees[$key]);
                $this->events[] = new AttendeeCancelledTheirAttendance($this->sessionId, $memberId);
            }
        }
    }
}
