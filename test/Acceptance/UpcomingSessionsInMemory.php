<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\UpcomingSessions\ListUpcomingSessions;
use LeanpubBookClub\Application\UpcomingSessions\UpcomingSession;
use LeanpubBookClub\Domain\Model\Session\ScheduledDate;
use LeanpubBookClub\Domain\Model\Session\SessionWasPlanned;

final class UpcomingSessionsInMemory implements ListUpcomingSessions
{
    /**
     * @var array<UpcomingSession>
     */
    private array $sessions = [];

    public function whenSessionWasPlanned(SessionWasPlanned $event): void
    {
        $this->sessions[] = new UpcomingSession(
            $event->sessionId()->asString(),
            $event->date()->asString(),
            $event->description()
        );
    }

    public function upcomingSessions(DateTimeImmutable $currentTime): array
    {
        $currentTimeAsComparableString = ScheduledDate::fromDateTime($currentTime)->asString();

        return array_filter(
            $this->sessions,
            function (UpcomingSession $session) use ($currentTimeAsComparableString) {
                return $session->date() >= $currentTimeAsComparableString;
            }
        );
    }
}
