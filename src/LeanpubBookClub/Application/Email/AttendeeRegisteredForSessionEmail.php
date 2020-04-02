<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\Members\Member;

final class AttendeeRegisteredForSessionEmail implements Email
{
    private Member $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function recipient(): string
    {
        return $this->member->emailAddress()->asString();
    }

    public function subject(): string
    {
        return 'attendee_registered_for_session_email.subject';
    }

    public function template(): string
    {
        return 'email/attendee_registered_for_session.html.twig';
    }

    public function templateVariables(): array
    {
        return [];
    }
}
