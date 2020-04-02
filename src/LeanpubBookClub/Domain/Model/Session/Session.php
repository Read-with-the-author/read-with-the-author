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
     * @var array<Attendee> & Attendee[]
     */
    private array $attendees = [];

    private bool $wasClosed = false;

    private ?string $urlForCall = null;

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
        int $maximumNumberOfAttendees
    ): self {
        $session = new self($sessionId, $date, $description, $maximumNumberOfAttendees);

        $session->events[] = new SessionWasPlanned($sessionId, $date, $description, $maximumNumberOfAttendees);

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
            if ($attendee->memberId()->equals($memberId)) {
                // No need to register the same attendee again
                return;
            }
        }

        $this->attendees[] = new Attendee($this->sessionId, $memberId);

        $this->events[] = new AttendeeRegisteredForSession($this->sessionId, $memberId, $this->date);

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
            if ($attendee->memberId()->equals($memberId)) {
                unset($this->attendees[$key]);
                $this->events[] = new AttendeeCancelledTheirAttendance($this->sessionId, $memberId);
            }
        }
    }

    public function setCallUrl(string $urlForCall): void
    {
        if ($urlForCall === $this->urlForCall) {
            return;
        }

        $this->urlForCall = $urlForCall;

        $this->events[] = new UrlForCallWasUpdated($this->sessionId, $urlForCall);
    }

    public function update(string $description, string $urlForCall): void
    {
        $this->setDescription($description);

        $this->setCallUrl($urlForCall);
    }

    private function setDescription(string $description): void
    {
        if ($this->description === $description) {
            return;
        }

        $this->description = $description;

        $this->events[] = new DescriptionWasUpdated($this->sessionId, $description);
    }
}
