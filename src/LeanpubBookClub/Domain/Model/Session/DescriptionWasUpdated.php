<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

final class DescriptionWasUpdated
{
    private SessionId $sessionId;

    private string $newDescription;

    public function __construct(SessionId $sessionId, string $newDescription)
    {
        $this->sessionId = $sessionId;
        $this->newDescription = $newDescription;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function newDescription(): string
    {
        return $this->newDescription;
    }
}
