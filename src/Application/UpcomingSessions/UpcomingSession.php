<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

final class UpcomingSession
{
    private string $sessionId;

    private string $date;

    private string $description;

    private bool $memberIsRegisteredAsAttendee;

    public function __construct(
        string $sessionId,
        string $date,
        string $description,
        bool $memberIsRegisteredAsAttendee
    ) {
        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
        $this->memberIsRegisteredAsAttendee = $memberIsRegisteredAsAttendee;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function memberIsRegisteredAsAttendee(): bool
    {
        return $this->memberIsRegisteredAsAttendee;
    }

    public function withActiveMemberRegisteredAsAttendee(): self
    {
        return new self(
            $this->sessionId,
            $this->date,
            $this->description,
            true
        );
    }
}
