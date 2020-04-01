<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Gherkin\Node\TableNode;
use BehatExpectException\ExpectException;
use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\CancelAttendance;
use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrlForSession;
use LeanpubBookClub\Application\SessionCall\SetCallUrl;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use PHPUnit\Framework\Assert;
use RuntimeException;

final class ParticipationContext extends FeatureContext
{
    use ExpectException;

    private ?string $sessionId = null;

    private ?string $plannedSessionId = null;

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
     * @Given the administrator has scheduled a session for :date
     */
    public function theAdministratorSchedulesASessionForDateWithDescription(string $date, string $description = 'Description'): void
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
            if ($this->plannedSessionId === $upcomingSession->sessionId()
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
            new AttendSession($this->sessionId, $this->leanpubInvoiceId)
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
            new CancelAttendance($this->sessionId, $this->leanpubInvoiceId)
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
            $this->sessionId,
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
            $this->sessionId,
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
     * @Then this session should not show up in the list of upcoming sessions
     */
    public function thisSessionShouldNotShowUpInTheListOfUpcomingSessions(): void
    {
        Assert::assertNotNull($this->plannedSessionId);
        Assert::assertNotNull($this->leanpubInvoiceId);

        $upcomingSessions = $this->application()->listUpcomingSessions(
            LeanpubInvoiceId::fromString($this->leanpubInvoiceId)
        );

        foreach ($upcomingSessions as $session) {
            if ($session->sessionId() === $this->plannedSessionId) {
                throw new RuntimeException('The session should not appear in the list of upcoming sessions');
            }
        }
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

    /**
     * @When the member requests the call URL for this session
     */
    public function theMemberRequestsTheCallURLForThisSession(): void
    {
        $this->mayFail(function () {
            Assert::assertNotNull($this->sessionId);

            $this->application()->getCallUrlForSession($this->sessionId);
        });
    }

    /**
     * @Then the it fails because it has not been provided yet
     */
    public function theCallURLCanNotBeDeterminedBecauseItHasNotBeenProvidedYet(): void
    {
        $this->assertCaughtExceptionMatches(CouldNotGetCallUrlForSession::class);
    }

    /**
     * @When the administrator sets the call URL to :callUrl
     */
    public function theMemberTheAdministratorSetTheCallUrlTo(string $callUrl): void
    {
        Assert::assertNotNull($this->sessionId);

        $this->application()->setCallUrl(
            new SetCallUrl($this->sessionId, $callUrl)
        );
    }

    /**
     * @Then the call URL for this session will be :expectedCallUrl
     */
    public function theCallUrlForThisSessionWillBe(string $expectedCallUrl): void
    {
        Assert::assertNotNull($this->sessionId);

        $actualUrl = $this->application()->getCallUrlForSession($this->sessionId);

        Assert::assertEquals($expectedCallUrl, $actualUrl);
    }

    /**
     * @Then this session should show up in the list of upcoming sessions for administrators:
     * @param TableNode<mixed> $table
     */
    public function thisSessionShouldShowUpInTheListOfUpcomingSessionsForAdministrators(TableNode $table): void
    {
        Assert::assertNotNull($this->plannedSessionId);
        Assert::assertNotNull($this->plannedSessionDescription);

        $expectedDetails = $table->getRowsHash();
        foreach ($this->application()->listUpcomingSessionsForAdministrator() as $upcomingSession) {
            if ($this->plannedSessionId === $upcomingSession->sessionId()
                && $this->plannedSessionDescription === $upcomingSession->description()) {

                Assert::assertEquals($expectedDetails['Date'], $upcomingSession->date($this->authorTimeZone()));
                Assert::assertEquals($expectedDetails['Time'], $upcomingSession->time($this->authorTimeZone()));

                return;
            }
        }

        throw new RuntimeException('Planned session not found in list of upcoming sessions');
    }
}
