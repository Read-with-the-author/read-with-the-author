<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use InvalidArgumentException;
use LeanpubBookClub\Domain\Model\Common\EntityTestCase;

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
            $aDescription
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
            $emptyDescription = ''
        );
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
}
