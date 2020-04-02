<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;

final class SendEmail
{
    private Mailer $mailer;

    private EventDispatcher $eventDispatcher;

    private Members $members;

    private Sessions $sessions;

    public function __construct(Mailer $mailer, EventDispatcher $eventDispatcher, Members $members, Sessions $sessions)
    {
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->members = $members;
        $this->sessions = $sessions;
    }

    public function whenAnAccessTokenWasGenerated(AnAccessTokenWasGenerated $event): void
    {
        $email = new AccessTokenEmail(
            $event->emailAddress(),
            $event->accessToken()
        );

        $this->sendEmail($email);
    }

    public function whenAttendeeRegisteredForSession(AttendeeRegisteredForSession $event): void
    {
        $member = $this->members->getOneById($event->memberId());
        $session = $this->sessions->getSessionForAdministrator($event->sessionId());

        $email = new AttendeeRegisteredForSessionEmail($member, $session);

        $this->sendEmail($email);
    }

    private function sendEmail(Email $email): void
    {
        $this->mailer->send($email);

        $this->eventDispatcher->dispatch(new EmailWasSent($email));
    }
}
