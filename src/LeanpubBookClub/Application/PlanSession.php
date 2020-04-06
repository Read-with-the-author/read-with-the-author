<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Session\Duration;
use LeanpubBookClub\Domain\Model\Session\ScheduledDate;

final class PlanSession
{
    private string $date;

    private string $timeZone;

    private int $duration;

    private string $description;

    private int $maximumNumberOfParticipants;

    public function __construct(string $date, string $timeZone, int $duration, string $description, int $maximumNumberOfParticipants)
    {
        $this->date = $date;
        $this->timeZone = $timeZone;
        $this->duration = $duration;
        $this->description = $description;
        $this->maximumNumberOfParticipants = $maximumNumberOfParticipants;
    }

    public function date(): ScheduledDate
    {
        return ScheduledDate::fromString($this->date, $this->timeZone);
    }

    public function description(): string
    {
        return $this->description;
    }

    public function maximumNumberOfParticipants(): int
    {
        return $this->maximumNumberOfParticipants;
    }

    public function duration(): Duration
    {
        return Duration::fromMinutes($this->duration);
    }
}
