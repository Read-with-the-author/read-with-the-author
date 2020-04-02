<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Members\Member;

final class MemberBuilder
{
    private string $memberId;

    private string $emailAddress;

    private string $timeZone;

    private function __construct()
    {
        $this->memberId = 'jP6LfQ3UkfOvZTLZLNfDfg';
        $this->emailAddress = 'matthias@matthiasnoback.nl';
        $this->timeZone = 'Europe/Amsterdam';
    }

    public static function create(): self
    {
        return new self();
    }

    public function build(): Member
    {
        return new Member(
            $this->memberId,
            $this->timeZone,
            $this->emailAddress
        );
    }
}
