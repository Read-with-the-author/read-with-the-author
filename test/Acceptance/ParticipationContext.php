<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Gherkin\Node\TableNode;
use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\CancelAttendance;
use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use PHPUnit\Framework\Assert;
use RuntimeException;

final class ParticipationContext extends FeatureContext
{
    private ?SessionId $sessionId = null;

    private ?SessionId $plannedSessionId = null;

    private ?string $plannedSessionDescription = null;

    private ?string $leanpubInvoiceId = null;

    private string $memberTimeZone = 'Europe/Amsterdam';

    /**
     * @Given today is :date
     */
    public function todayIs(string $date): void
    {
        $this->serviceContainer()->setCurrentDate($date);
    }

    /**
     * @When the administrator schedules a session for :date with description :description
     */
    public function theAdministratorSchedulesASessionForDateWithDescription(string $date, string $description): void
    {
        $this->plannedSessionId = $this->application()->planSession(
            new PlanSession($date, $this->authorTimeZone(), $description, 10)
        );
        $this->plannedSessionDescription = $description;
    }

    /**
     * @Then this session should show up in the list of upcoming sessions for the active member with the following details:
     * @param TableNode<mixed> $table
     */
    public function thisSessionShouldShowUpInTheListOfUpcomingSessions(TableNode $table): void
    {
        Assert::assertNotNull($this->plannedSessionId);
        Assert::assertNotNull($this->plannedSessionDescription);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $expectedDetails = $table->getRowsHash();
        foreach ($this->application()->listUpcomingSessions(
            LeanpubInvoiceId::fromString($this->leanpubInvoiceId)) as $upcomingSession) {
            if ($this->plannedSessionId->asString() === $upcomingSession->sessionId()
                && $this->plannedSessionDescription === $upcomingSession->description()) {

                Assert::assertEquals($expectedDetails['Date'], $upcomingSession->date($this->memberTimeZone));
                Assert::assertEquals($expectedDetails['Time'], $upcomingSession->time($this->memberTimeZone));

                return;
            }
        }

        throw new RuntimeException('Planned session not found in list of upcoming sessions');
    }

    /**
     * @Given an upcoming session
     */
    public function anUpcomingSession(): void
    {
        $this->sessionId = $this->application()->planSession(
            new PlanSession('2020-04-01 20:00', $this->authorTimeZone(), 'Chapter 1', 10)
        );
    }

    /**
     * @Given a member who has been granted access
     */
    public function aMemberWhoHasBeenGrantedAccess(): void
    {
        $this->leanpubInvoiceId = 'jP6LfQ3UkfOvZTLZLNfDfg';

        $this->application()->requestAccess(
            new RequestAccess($this->leanpubInvoiceId, 'info@matthiasnoback.nl', $this->memberTimeZone)
        );
        $this->application()->grantAccess(LeanpubInvoiceId::fromString($this->leanpubInvoiceId));
    }

    /**
     * @When the member registers themselves as a participant of the session
     * @Given the member has registered themselves as a participant of the session
     */
    public function theMemberRegistersThemselvesAsAParticipantOfTheSession(): void
    {
        Assert::assertNotNull($this->sessionId);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $this->application()->attendSession(
            new AttendSession($this->sessionId->asString(), $this->leanpubInvoiceId)
        );
    }

    /**
     * @When they cancel their attendance
     */
    public function theMemberCancelsTheirAttendance(): void
    {
        Assert::assertNotNull($this->sessionId);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $this->application()->cancelAttendance(
            new CancelAttendance($this->sessionId->asString(), $this->leanpubInvoiceId)
        );
    }

    /**
     * @Then the list of upcoming sessions should indicate that they have been registered as a participant
     */
    public function theListOfUpcomingSessionsShouldIndicateThatTheyHaveBeenRegisteredAsAParticipant(): void
    {
        Assert::assertNotNull($this->sessionId);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $this->memberShouldBeRegisteredAsParticipantOfSession(
            $this->sessionId->asString(),
            $this->leanpubInvoiceId,
            true
        );
    }

    /**
     * @Then the list of upcoming sessions should indicate that they have not been registered as a participant
     */
    public function theListOfUpcomingSessionsShouldIndicateThatTheyHaveNotBeenRegisteredAsAParticipant(): void
    {
        Assert::assertNotNull($this->sessionId);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $this->memberShouldBeRegisteredAsParticipantOfSession(
            $this->sessionId->asString(),
            $this->leanpubInvoiceId,
            false
        );
    }

    private function memberShouldBeRegisteredAsParticipantOfSession(
        string $sessionId,
        string $memberId,
        bool $expectedToAttend
    ): void {
        $upcomingSessions = $this->application()->listUpcomingSessions(
            LeanpubInvoiceId::fromString($memberId)
        );

        foreach ($upcomingSessions as $session) {
            if ($session->sessionId() === $sessionId) {
                Assert::assertSame($expectedToAttend, $session->memberIsRegisteredAsAttendee());
                return;
            }
        }

        throw new RuntimeException('The list of upcoming sessions did not contain session ' . $sessionId);
    }

    /**
     * @Given the author's time zone is :timeZone
     */
    public function theAuthorsTimeZoneIs(string $timeZone): void
    {
        /*
         * This is hard-coded in the test environment, and in prod it's a configuration value. Let's at least assert
         * that the assumption is correct
         */
        Assert::assertEquals($timeZone, $this->authorTimeZone());
    }

    /**
     * @Given the member's time zone is :timeZone
     */
    public function theMembersTimeZoneIs(string $timeZone): void
    {
        Assert::assertNotNull($this->leanpubInvoiceId);

        $this->application()->updateTimeZone(new UpdateTimeZone($this->leanpubInvoiceId, $timeZone));
        $this->memberTimeZone = $timeZone;
    }

    private function authorTimeZone(): string
    {
        return $this->serviceContainer()->authorTimeZone()->asString();
    }
}
