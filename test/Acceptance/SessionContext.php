<?php
declare(strict_types=1);

namespace Test\Acceptance;

use DateTimeImmutable;
use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use PHPUnit\Framework\Assert;
use RuntimeException;

final class SessionContext extends FeatureContext
{
    private ?SessionId $plannedSessionId = null;

    private ?string $plannedSessionDescription = null;

    /**
     * @Given today is :date
     */
    public function todayIs(string $date)
    {
        $currentTime = DateTimeImmutable::createFromFormat('Y-m-d', $date);
        Assert::assertInstanceOf(DateTimeImmutable::class, $currentTime);

        $this->serviceContainer()->setCurrentTime($currentTime);
    }

    /**
     * @When the administrator schedules a session for :date with description :description
     */
    public function theAdministratorSchedulesASessionFor20200420WithDescription(string $date, string $description): void
    {
        $this->plannedSessionId = $this->application()->planSession(new PlanSession($date, $description));
        $this->plannedSessionDescription = $description;
    }

    /**
     * @Then this session should show up in the list of upcoming sessions
     */
    public function thisSessionShouldShowUpInTheListOfUpcomingSessions()
    {
        Assert::assertNotNull($this->plannedSessionId);
        Assert::assertNotNull($this->plannedSessionDescription);

        foreach ($this->application()->listUpcomingSessions() as $upcomingSession) {
            if ($this->plannedSessionId->asString() === $upcomingSession->sessionId()
                && $this->plannedSessionDescription === $upcomingSession->description()) {
                return;
            }
        }

        throw new RuntimeException('Planned session not found in list of upcoming sessions');
    }
}
