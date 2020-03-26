<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Session\ScheduledDate;

final class PlanSession
{
    private string $date;

    private string $description;

    public function __construct(string $date, string $description)
    {
        $this->date = $date;
        $this->description = $description;
    }

    public function date(): ScheduledDate
    {
        return ScheduledDate::fromString($this->date);
    }

    public function description(): string
    {
        return $this->description;
    }
}
