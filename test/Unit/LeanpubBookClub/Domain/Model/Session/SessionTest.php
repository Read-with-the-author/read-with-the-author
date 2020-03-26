<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Common\EntityTestCase;
use LeanpubBookClub\Domain\Model\Member\MemberId;

final class SessionTest extends EntityTestCase
{
    /**
     * @test
     */
    public function it_can_be_planned_for_a_given_date(): void
    {
        $aSessionId = $this->aSessionId();
        $aDate = $this->aDate();
        $aDescription = $this->aDescription();

        $session = Session::plan(
            $aSessionId,
            $aDate,
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

        self::assertEquals(
            [
                new AttendeeRegisteredForSession($session->sessionId(), $memberId)
            ],
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
                new AttendeeRegisteredForSession($session->sessionId(), $memberId),
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
                new AttendeeRegisteredForSession($session->sessionId(), $memberId)
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

        $session->attend($this->anotherMemberId());

        self::assertEquals([], $session->releaseEvents());
    }

    private function aSessionId(): SessionId
    {
        return SessionId::fromString('48e42502-79ee-47ac-b085-4571fc0f719c');
    }

    private function aDate(): ScheduledDate
    {
        return ScheduledDate::fromString('2020-03-26 09:21');
    }

    private function aDescription(): string
    {
        return 'Description of this session';
    }

    private function aMaximumNumberOfParticipants(): int
    {
        return 10;
    }

    private function aSession(int $maximumNumberOfParticipants = null): Session
    {
        $session = Session::plan(
            $this->aSessionId(),
            $this->aDate(),
            $this->aDescription(),
            $maximumNumberOfParticipants ?? $this->aMaximumNumberOfParticipants()
        );

        $session->releaseEvents();

        return $session;
    }

    private function aSessionWithMaximumNumberOfAttendees(int $number): Session
    {
        return $this->aSession($number);
    }

    private function aMemberId(): MemberId
    {
        return MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703');
    }

    private function anotherMemberId(): MemberId
    {
        return MemberId::fromString('9aec8d8d-6cbc-4502-9583-06512e18ff86');
    }
}
