<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Session\SessionId;

final class UpdateSession
{
    private string $sessionId;

    private string $description;

    private string $urlForCall;

    public function __construct(string $sessionId, string $description, string $urlForCall)
    {
        $this->sessionId = $sessionId;
        $this->description = $description;
        $this->urlForCall = $urlForCall;
    }

    public function sessionId(): SessionId
    {
        return SessionId::fromString($this->sessionId);
    }

    public function description(): string
    {
        return $this->description;
    }

    public function urlForCall(): string
    {
        return $this->urlForCall;
    }
}
