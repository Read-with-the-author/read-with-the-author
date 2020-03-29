<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Domain\Model\Member\EmailAddress;

interface Email
{
    public function recipient(): EmailAddress;

    public function subject(): string;

    public function template(): string;

    /**
     * @return array<string,mixed>
     */
    public function templateVariables(): array;
}
