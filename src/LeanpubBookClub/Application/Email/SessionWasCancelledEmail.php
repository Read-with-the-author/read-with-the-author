<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;

final class SessionWasCancelledEmail implements Email
{
    private Member $member;

    private SessionForAdministrator $session;

    public function __construct(Member $member, SessionForAdministrator $session)
    {
        $this->member = $member;
        $this->session = $session;
    }

    public function recipient(): string
    {
        return $this->member->emailAddress()->asString();
    }

    public function subject(): string
    {
        return 'session_was_cancelled_email.subject';
    }

    public function template(): string
    {
        return 'email/session_was_cancelled.html.twig';
    }

    public function templateVariables(): array
    {
        return [
            'session' => $this->session,
            'member' => $this->member
        ];
    }
}
