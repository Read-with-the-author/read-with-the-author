<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

final class UrlForCallWasUpdated
{
    private SessionId $sessionId;

    private string $callUrl;

    public function __construct(SessionId $sessionId, string $urlForCall)
    {
        $this->sessionId = $sessionId;
        $this->callUrl = $urlForCall;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function callUrl(): string
    {
        return $this->callUrl;
    }
}
