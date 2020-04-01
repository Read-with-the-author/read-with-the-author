<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Session\ScheduledDate;

final class PlanSession
{
    private string $date;

    private string $timeZone;

    private string $description;

    private int $maximumNumberOfParticipants;

    public function __construct(string $date, string $timeZone, string $description, int $maximumNumberOfParticipants)
    {
        $this->date = $date;
        $this->timeZone = $timeZone;
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
}
