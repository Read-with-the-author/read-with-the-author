<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;

final class SendAccessTokenEmail
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function whenAnAccessTokenWasGenerated(AnAccessTokenWasGenerated $event): void
    {
        $this->mailer->send(
            new AccessTokenEmail(
                $event->emailAddress(),
                $event->accessToken()
            )
        );
    }
}
