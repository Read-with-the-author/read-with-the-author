<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\UpcomingSessions\ListUpcomingSessions;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSessionForAdministrator;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\AttendeeCancelledTheirAttendance;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;

final class UpcomingSessionsInMemory implements ListUpcomingSessions
{
    /**
     * @var array<string,UpcomingSession>
     */
    private array $sessions = [];

    /**
     * @var array<string,UpcomingSessionForAdministrator>
     */
    private array $sessionsForAdministrator = [];

    /**
     * @var array<string,array<string,bool>>
     */
    private array $attendees = [];

    public function whenSessionWasPlanned(SessionWasPlanned $event): void
    {
        $this->sessions[$event->sessionId()->asString()] = new UpcomingSession(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description(),
            false
        );

        $this->sessionsForAdministrator[$event->sessionId()->asString()] = new UpcomingSessionForAdministrator(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description(),
            $event->maximumNumberOfAttendees()
        );
    }

    public function whenAttendeeRegisteredForSession(AttendeeRegisteredForSession $event): void
    {
        $this->attendees[$event->sessionId()->asString()][$event->memberId()->asString()] = true;
    }

    public function whenAttendeeCancelledTheirAttendance(AttendeeCancelledTheirAttendance $event): void
    {
        $this->attendees[$event->sessionId()->asString()][$event->memberId()->asString()] = false;
    }

    public function upcomingSessions(DateTimeImmutable $currentTime, LeanpubInvoiceId $activeMemberId): array
    {
        return array_map(
            function (UpcomingSession $upcomingSession) use ($activeMemberId): UpcomingSession {
                if ($this->attendees[$upcomingSession->sessionId()][$activeMemberId->asString()] ?? false) {
                    return $upcomingSession->withActiveMemberRegisteredAsAttendee();
                }

                return $upcomingSession;
            },
            array_filter(
                $this->sessions,
                function (UpcomingSession $session) use ($currentTime): bool {
                    return $session->isToBeConsideredUpcoming($currentTime);
                }
            )
        );
    }

    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array
    {
        return array_map(
            function (UpcomingSessionForAdministrator $upcomingSession): UpcomingSessionForAdministrator {
                $attendeesForSession = $this->attendees[$upcomingSession->sessionId()] ?? [];

                return $upcomingSession->withNumberOfAttendees(count($attendeesForSession));
            },
            array_filter(
                $this->sessionsForAdministrator,
                function (UpcomingSessionForAdministrator $session) use ($currentTime): bool {
                    return $session->isToBeConsideredUpcoming($currentTime);
                }
            )
        );
    }
}
