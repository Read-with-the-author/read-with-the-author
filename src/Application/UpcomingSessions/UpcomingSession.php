<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

final class UpcomingSession
{
    private string $sessionId;

    private string $date;

    private string $description;

    public function __construct(string $sessionId, string $date, string $description)
    {
        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
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
}
