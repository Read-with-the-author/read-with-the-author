<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\SessionCall;

use LeanpubBookClub\Domain\Model\Session\SessionId;

final class SetCallUrl
{
    private string $sessionId;

    private string $callUrl;

    public function __construct(string $sessionId, string $callUrl)
    {
        $this->sessionId = $sessionId;
        $this->callUrl = $callUrl;
    }

    public function sessionId(): SessionId
    {
        return SessionId::fromString($this->sessionId);
    }

    public function callUrl(): string
    {
        return $this->callUrl;
    }
}
