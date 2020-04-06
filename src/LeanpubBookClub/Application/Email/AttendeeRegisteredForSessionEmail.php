<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use DateTime;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\UpcomingSessions\SessionForAdministrator;
use Spatie\CalendarLinks\Link;

final class AttendeeRegisteredForSessionEmail implements Email
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
        return 'attendee_registered_for_session_email.subject';
    }

    public function template(): string
    {
        return 'email/attendee_registered_for_session.html.twig';
    }

    public function templateVariables(): array
    {
        $calendarLink = (new Link(
            'Read with the author session',
            DateTime::createFromImmutable(
                $this->session->dateTime('UTC')
            ),
            DateTime::createFromImmutable(
                $this->session->endTimeDateTime('UTC')
            )
        ))->description($this->session->description()); // @todo abbreviate description?

        return [
            'session' => $this->session,
            'member' => $this->member,
            'calendarLink' => $calendarLink
        ];
    }
}
