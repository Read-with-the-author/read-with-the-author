<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Email\Email;
use LeanpubBookClub\Application\Email\Mailer;

final class MailerSpy implements Mailer
{
    /**
     * @var array<Email>
     */
    private array $sentEmails = [];

    public function send(Email $email): void
    {
        $this->sentEmails[] = $email;
    }

    /**
     * @return array<Email> & Email[]
     */
    public function sentEmails(): array
    {
        return $this->sentEmails;
    }
}
