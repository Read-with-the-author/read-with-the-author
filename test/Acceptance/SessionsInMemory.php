<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\UpcomingSessions\CouldNotFindSession;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Application\UpcomingSessions\SessionForMember;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\AttendeeCancelledTheirAttendance;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;
use LeanpubBookClub\Domain\Model\Session\UrlForCallWasUpdated;

final class SessionsInMemory implements Sessions
{
    /**
     * @var array<string,SessionForMember> & SessionForMember[]
     */
    private array $sessionsForMembers = [];

    /**
     * @var array<string,SessionForAdministrator> & SessionForAdministrator[]
     */
    private array $sessionsForAdministrator = [];

    /**
     * @var array<string,array<string,bool>>
     */
    private array $attendees = [];

    public function whenSessionWasPlanned(SessionWasPlanned $event): void
    {
        $this->sessionsForMembers[$event->sessionId()->asString()] = new SessionForMember(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description()
        );

        $this->sessionsForAdministrator[$event->sessionId()->asString()] = new SessionForAdministrator(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description(),
            $event->maximumNumberOfAttendees()
        );
    }

    public function whenUrlForCallWasUpdated(UrlForCallWasUpdated $event): void
    {
        $this->updateSessionForMember(
            $this->sessionForMember($event->sessionId())->withUrlForCall($event->callUrl())
        );

        $this->updateSessionForAdministrator(
            $this->sessionForAdministrator($event->sessionId())->withUrlForCall($event->callUrl())
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
            function (SessionForMember $session) use ($activeMemberId): SessionForMember {
                return $this->updateSessionForMemberReadModel($session, $activeMemberId);
            },
            array_filter(
                $this->sessionsForMembers,
                function (SessionForMember $session) use ($currentTime): bool {
                    return $session->isToBeConsideredUpcoming($currentTime);
                }
            )
        );
    }

    public function getSessionForMember(SessionId $sessionId, LeanpubInvoiceId $memberId): SessionForMember
    {
        if (!isset($this->sessionsForMembers[$sessionId->asString()])) {
            throw CouldNotFindSession::withId($sessionId);
        }

        return $this->updateSessionForMemberReadModel($this->sessionsForMembers[$sessionId->asString()], $memberId);
    }

    public function upcomingSessionsForAdministrator(DateTimeImmutable $currentTime): array
    {
        return array_map(
            [$this, 'updateUpcomingSessionForAdministratorReadModel'],
            array_filter(
                $this->sessionsForAdministrator,
                function (SessionForAdministrator $session) use ($currentTime): bool {
                    return $session->isToBeConsideredUpcoming($currentTime);
                }
            )
        );
    }

    public function getSessionForAdministrator(SessionId $sessionId): SessionForAdministrator
    {
        if (!isset($this->sessionsForAdministrator[$sessionId->asString()])) {
            throw CouldNotFindSession::withId($sessionId);
        }

        return $this->updateUpcomingSessionForAdministratorReadModel(
            $this->sessionsForAdministrator[$sessionId->asString()]
        );
    }

    private function updateUpcomingSessionForAdministratorReadModel(
        SessionForAdministrator $session
    ): SessionForAdministrator {
        $attendeesForSession = $this->attendees[$session->sessionId()] ?? [];

        return $session->withNumberOfAttendees(count($attendeesForSession));
    }

    private function updateSessionForMemberReadModel(
        SessionForMember $session,
        LeanpubInvoiceId $activeMemberId
    ): SessionForMember {
        $isActiveMemberRegisteredAsAttendee = $this->attendees[$session->sessionId()][$activeMemberId->asString()] ?? false;

        return $session->withActiveMemberRegisteredAsAttendee($isActiveMemberRegisteredAsAttendee);
    }

    private function sessionForMember(SessionId $sessionId): SessionForMember
    {
        return $this->sessionsForMembers[$sessionId->asString()];
    }

    private function updateSessionForMember(SessionForMember $updatedSession): void
    {
        $this->sessionsForMembers[$updatedSession->sessionId()] = $updatedSession;
    }

    private function sessionForAdministrator(SessionId $sessionId): SessionForAdministrator
    {
        return $this->sessionsForAdministrator[$sessionId->asString()];
    }

    private function updateSessionForAdministrator(SessionForAdministrator $updatedSession): void
    {
        $this->sessionsForAdministrator[$updatedSession->sessionId()] = $updatedSession;
    }
}
