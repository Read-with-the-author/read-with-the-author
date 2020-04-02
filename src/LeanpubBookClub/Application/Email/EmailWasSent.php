<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

final class EmailWasSent
{
    private Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function email(): Email
    {
        return $this->email;
    }
}
