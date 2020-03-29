<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

interface Mailer
{
    public function send(Email $email): void;
}
