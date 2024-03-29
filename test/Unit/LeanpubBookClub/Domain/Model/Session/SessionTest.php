<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Common\EntityTestCase;

final class SessionTest extends EntityTestCase
{
    use SessionFactoryMethods;

    /**
     * @test
     */
    public function it_can_be_planned_for_a_given_date(): void
    {
        $aSessionId = $this->aSessionId();
        $aDate = $this->aDate();
        $aDescription = $this->aDescription();
        $duration = $this->aDuration();

        $session = Session::plan(
            $aSessionId,
            $aDate,
            $duration,
            $aDescription,
            $this->aMaximumNumberOfParticipants()
        );

        self::assertArrayContainsObjectOfType(SessionWasPlanned::class, $session->releaseEvents());

        self::assertEquals($aSessionId, $session->sessionId());
    }

    /**
     * @test
     */
    public function the_description_should_not_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('description');

        Session::plan(
            $this->aSessionId(),
            $this->aDate(),
            $this->aDuration(),
            $emptyDescription = '',
            $this->aMaximumNumberOfParticipants()
        );
    }

    /**
     * @test
     */
    public function the_maximum_number_of_participants_should_be_greater_than_0(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('maximum number of participants');

        Session::plan(
            $this->aSessionId(),
            $this->aDate(),
            $this->aDuration(),
            $this->aDescription(),
            $tooLow = 0
        );
    }

    /**
     * @test
     */
    public function a_member_can_attend_it(): void
    {
        $session = $this->aSession();

        $memberId = $this->aMemberId();
        $session->attend($memberId);

        self::assertArrayContainsObjectOfType(AttendeeRegisteredForSession::class, $session->releaseEvents());
    }

    /**
     * @test
     */
    public function a_member_can_cancel_attendance(): void
    {
        $session = $this->aSession();
        $memberId = $this->aMemberId();
        $session->attend($memberId);
        $session->releaseEvents();

        $session->cancelAttendance($memberId);

        self::assertEquals(
            [
                new AttendeeCancelledTheirAttendance($session->sessionId(), $memberId)
            ],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function cancelling_attendance_again_has_no_effect(): void
    {
        $session = $this->aSession();
        $memberId = $this->aMemberId();
        $session->attend($memberId);
        $session->cancelAttendance($memberId);

        $session->releaseEvents();

        $session->cancelAttendance($memberId);

        self::assertEquals(
            [],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function if_the_maximum_number_of_attendees_was_reached_the_session_is_closed_for_registration(): void
    {
        $session = $this->aSessionWithMaximumNumberOfAttendees(1);

        $memberId = $this->aMemberId();
        $session->attend($memberId);

        self::assertEquals(
            [
                new AttendeeRegisteredForSession($session->sessionId(), $memberId, $this->aDate()),
                new SessionWasClosedForRegistration($session->sessionId())
            ],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function a_second_registration_for_the_same_member_gets_ignored(): void
    {
        $session = $this->aSessionWithMaximumNumberOfAttendees(2);

        $memberId = $this->aMemberId();
        $session->attend($memberId);

        $sameMemberId = $memberId;
        $session->attend($sameMemberId);

        self::assertEquals(
            [
                new AttendeeRegisteredForSession($session->sessionId(), $memberId, $this->aDate())
                // The registration wasn't closed
            ],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function when_a_session_was_closed_it_is_impossible_to_register_as_an_attendee(): void
    {
        $session = $this->aSessionWithMaximumNumberOfAttendees(1);
        $session->attend($this->aMemberId());
        $session->releaseEvents();

        $this->expectException(CouldNotAttendSession::class);
        $this->expectExceptionMessage('maximum_number_of_attendees');

        $session->attend($this->anotherMemberId());
    }

    /**
     * @test
     */
    public function the_url_for_the_call_will_be_provided_later(): void
    {
        $session = $this->aSession();

        $urlForCall = 'https://whereby.com/matthiasnoback';
        $session->setCallUrl($urlForCall);

        self::assertEquals(
            [
                new UrlForCallWasUpdated($session->sessionId(), $urlForCall)
            ],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function it_can_be_cancelled(): void
    {
        $session = $this->aSession();

        $session->cancel();

        self::assertEquals(
            [
                new SessionWasCancelled($session->sessionId())
            ],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function when_cancelling_a_session_member_attendances_will_be_cancelled_to(): void
    {
        $session = $this->aSession();

        $memberId1 = $this->aMemberId();
        $session->attend($memberId1);
        $memberId2 = $this->anotherMemberId();
        $session->attend($memberId2);

        $session->cancel();

        $events = $session->releaseEvents();
        self::assertContainsEquals(
            new AttendanceWasCancelledBecauseSessionWasCancelled($session->sessionId(), $memberId1),
            $events
        );
        self::assertContainsEquals(
            new AttendanceWasCancelledBecauseSessionWasCancelled($session->sessionId(), $memberId2),
            $events
        );
    }

    /**
     * @test
     */
    public function cancelling_again_has_no_effect(): void
    {
        $session = $this->aSession();
        $session->cancel();

        $session->releaseEvents();
        $session->cancel();

        self::assertEquals(
            [],
            $session->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function you_can_not_attend_a_session_that_was_cancelled(): void
    {
        $session = $this->aSession();
        $session->cancel();

        $this->expectException(CouldNotAttendSession::class);

        $session->attend($this->aMemberId());
    }

    /**
     * @test
     */
    public function the_description_and_url_for_call_can_be_updated(): void
    {
        $session = $this->aSession();
        $session->setCallUrl('https://whereby.com/matthiasnoback');
        $session->releaseEvents();

        $newUrl = 'https://whereby.com/new-url-for-call';
        $newDescription = 'New description';
        $session->update($newDescription, $newUrl);

        self::assertEquals(
            [
                new DescriptionWasUpdated($session->sessionId(), $newDescription),
                new UrlForCallWasUpdated($session->sessionId(), $newUrl)
            ],
            $session->releaseEvents()
        );

        // Not a real update, produces no events
        $session->update($newDescription, $newUrl);
        self::assertEquals([], $session->releaseEvents());
    }
}
