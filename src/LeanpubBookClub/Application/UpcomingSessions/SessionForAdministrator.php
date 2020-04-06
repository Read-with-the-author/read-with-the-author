<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class SessionForAdministrator
{
    private string $sessionId;

    private string $date;

    private string $description;

    private ?string $urlForCall = null;

    private int $numberOfAttendees = 0;

    private int $maximumNumberOfAttendees;

    private int $duration;

    public function __construct(
        string $sessionId,
        string $date,
        int $duration,
        string $description,
        int $maximumNumberOfAttendees
    ) {
        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
        $this->maximumNumberOfAttendees = $maximumNumberOfAttendees;
        $this->duration = $duration;
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function date(string $timeZone): string
    {
        return $this->dateTime($timeZone)->format('l, F jS');
    }

    public function time(string $timeZone): string
    {
        return $this->dateTime($timeZone)->format('H:i');
    }

    public function description(): string
    {
        return $this->description;
    }

    public function maximumNumberOfAttendees(): int
    {
        return $this->maximumNumberOfAttendees;
    }

    public function urlForCall(): ?string
    {
        return $this->urlForCall;
    }

    public function numberOfAttendees(): int
    {
        return $this->numberOfAttendees;
    }

    public function attendeeStats(): string
    {
        return sprintf('%d/%d', $this->numberOfAttendees, $this->maximumNumberOfAttendees);
    }

    public function withNumberOfAttendees(int $numberOfAttendees): self
    {
        $copy = clone $this;

        $copy->numberOfAttendees = $numberOfAttendees;

        return $copy;
    }

    public function dateTime(string $timeZone): DateTimeImmutable
    {
        $dateTime = new DateTimeImmutable($this->date);

        $dateTime = $dateTime->setTimezone(TimeZone::fromString($timeZone)->asPhpDateTimeZone());

        return $dateTime;
    }

    public function endTimeDateTime(string $timeZone): DateTimeImmutable
    {
        $startTime = $this->dateTime($timeZone);

        return $startTime->modify(sprintf('+%d minutes', $this->duration));
    }

    public function withUrlForCall(?string $urlForCall): self
    {
        $copy = clone $this;

        $copy->urlForCall = $urlForCall;

        return $copy;
    }

    public function isToBeConsideredUpcoming(DateTimeImmutable $currentTime): bool
    {
        return $this->dateTime('UTC')->modify('+2 hours') >= $currentTime;
    }
}
