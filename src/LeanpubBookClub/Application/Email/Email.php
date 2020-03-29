<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

interface Email
{
    public function recipient(): string;

    public function subject(): string;

    public function template(): string;

    /**
     * @return array<string,mixed>
     */
    public function templateVariables(): array;
}
