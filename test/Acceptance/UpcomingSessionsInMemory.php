<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\UpcomingSessions\ListUpcomingSessions;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\ScheduledDate;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;

final class UpcomingSessionsInMemory implements ListUpcomingSessions
{
    /**
     * @var array<UpcomingSession>
     */
    private array $sessions = [];

    /**
     * @var array<string,array<string,bool>>
     */
    private array $attendees = [];

    public function whenSessionWasPlanned(SessionWasPlanned $event): void
    {
        $this->sessions[] = new UpcomingSession(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description(),
            false
        );
    }

    public function whenAttendeeRegisteredForSession(AttendeeRegisteredForSession $event): void
    {
        $this->attendees[$event->sessionId()->asString()][$event->memberId()->asString()] = true;
    }

    public function upcomingSessions(DateTimeImmutable $currentTime, MemberId $activeMemberId): array
    {
        $currentTimeAsComparableString = ScheduledDate::fromDateTime($currentTime)->asString();

        $upcomingSessions = array_filter(
            $this->sessions,
            function (UpcomingSession $session) use ($currentTimeAsComparableString) {
                return $session->date() >= $currentTimeAsComparableString;
            }
        );

        return array_map(
            function (UpcomingSession $upcomingSession) use ($activeMemberId): UpcomingSession {
                if ($this->attendees[$upcomingSession->sessionId()][$activeMemberId->asString()] ?? false) {
                    return $upcomingSession->withActiveMemberRegisteredAsAttendee();
                }

                return $upcomingSession;
            },
            $upcomingSessions
        );
    }
}
