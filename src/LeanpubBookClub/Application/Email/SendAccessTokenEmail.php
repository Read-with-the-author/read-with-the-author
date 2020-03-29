<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;

final class SendAccessTokenEmail
{
    private Mailer $mailer;

    private EventDispatcher $eventDispatcher;

    public function __construct(Mailer $mailer, EventDispatcher $eventDispatcher)
    {
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function whenAnAccessTokenWasGenerated(AnAccessTokenWasGenerated $event): void
    {
        $this->mailer->send(
            new AccessTokenEmail(
                $event->emailAddress(),
                $event->accessToken()
            )
        );

        $this->eventDispatcher->dispatch(new AccessTokenEmailWasSent($event->emailAddress()));
    }
}
