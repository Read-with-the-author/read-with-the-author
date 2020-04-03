<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class SessionForMember
{
    private string $sessionId;

    private string $date;

    private string $description;

    private bool $memberIsRegisteredAsAttendee;

    private ?string $urlForCall = null;

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

    public function date(string $memberTimeZone): string
    {
        return $this->dateTime($memberTimeZone)->format('l, F jS');
    }

    public function time(string $memberTimeZone): string
    {
        return $this->dateTime($memberTimeZone)->format('H:i');
    }

    public function description(): string
    {
        return $this->description;
    }

    public function memberIsRegisteredAsAttendee(): bool
    {
        return $this->memberIsRegisteredAsAttendee;
    }

    public function withActiveMemberRegisteredAsAttendee(bool $activeMemberIsRegistered): self
    {
        $copy = clone $this;

        $copy->memberIsRegisteredAsAttendee  = $activeMemberIsRegistered;

        return $copy;
    }

    private function dateTime(string $memberTimeZone): DateTimeImmutable
    {
        $dateTime = new DateTimeImmutable($this->date);

        $dateTime = $dateTime->setTimezone(TimeZone::fromString($memberTimeZone)->asPhpDateTimeZone());

        return $dateTime;
    }

    public function isToBeConsideredUpcoming(DateTimeImmutable $currentTime): bool
    {
        return $this->dateTime('UTC')->modify('+2 hours') >= $currentTime;
    }

    public function urlForCall(): ?string
    {
        return $this->urlForCall;
    }

    public function withUrlForCall(string $urlForCall): self
    {
        $copy = clone $this;

        $copy->urlForCall = $urlForCall;

        return $copy;
    }
}
