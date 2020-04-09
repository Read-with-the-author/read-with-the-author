<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use Ramsey\Uuid\Uuid;

trait SessionFactoryMethods
{
    private function aSessionId(): SessionId
    {
        return SessionId::fromString('48e42502-79ee-47ac-b085-4571fc0f719c');
    }

    private function aRandomSessionId(): SessionId
    {
        return SessionId::fromString(Uuid::uuid4()->toString());
    }

    private function aDate(): ScheduledDate
    {
        return ScheduledDate::fromString('2020-03-26 09:21');
    }

    private function aDescription(): string
    {
        return 'Description of this session';
    }

    private function anUpdatedDescription(): string
    {
        return 'The updated description of this session';
    }

    private function aMaximumNumberOfParticipants(): int
    {
        return 10;
    }

    private function aSession(int $maximumNumberOfParticipants = null): Session
    {
        $session = Session::plan(
            $this->aRandomSessionId(),
            $this->aDate(),
            $this->aDuration(),
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

    private function aMemberId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg');
    }

    private function anotherMemberId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString('6gbXPEDMOEMKCNwOykPvpg');
    }

    private function aDuration(): Duration
    {
        return Duration::fromMinutes(60);
    }

    private function aUrlForTheCall(): string
    {
        return 'https://whereby.com/matthiasnoback';
    }
}
