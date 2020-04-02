<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Session\AttendeeRegisteredForSession;

final class SendEmail
{
    private Mailer $mailer;

    private EventDispatcher $eventDispatcher;

    private Members $members;

    public function __construct(Mailer $mailer, EventDispatcher $eventDispatcher, Members $members)
    {
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->members = $members;
    }

    public function whenAnAccessTokenWasGenerated(AnAccessTokenWasGenerated $event): void
    {
        $email = new AccessTokenEmail(
            $event->emailAddress(),
            $event->accessToken()
        );

        $this->sendEmail($email);
    }

    private function sendEmail(AccessTokenEmail $email): void
    {
        $this->mailer->send($email);

        $this->eventDispatcher->dispatch(new EmailWasSent($email));
    }

    public function whenAttendeeRegisteredForSession(AttendeeRegisteredForSession $event): void
    {
        $member = $this->members->getOneById($event->memberId());

        $email = new AttendeeRegisteredForSessionEmail($member);

        $this->mailer->send($email);

        $this->eventDispatcher->dispatch(new EmailWasSent($email));
    }
}
