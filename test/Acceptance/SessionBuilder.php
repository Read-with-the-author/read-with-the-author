<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;

final class SessionBuilder
{
    private string $sessionId;
    private string $date;
    private string $description;
    private int $maximumNumberOfParticipants;

    private function __construct()
    {
        $this->sessionId = '48e42502-79ee-47ac-b085-4571fc0f719c';
        $this->date = '2020-04-01 20:00';
        $this->description = 'The description';
        $this->maximumNumberOfParticipants = 20;
    }

    public static function create(): self
    {
        return new self();
    }

    public function build(): SessionForAdministrator
    {
        return new SessionForAdministrator(
            $this->sessionId,
            $this->date,
            $this->description,
            $this->maximumNumberOfParticipants
        );
    }
}
