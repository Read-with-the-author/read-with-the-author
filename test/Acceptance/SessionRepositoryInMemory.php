<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Domain\Model\Session\Session;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class SessionRepositoryInMemory implements SessionRepository
{
    /**
     * @var array<string,Session>
     */
    private array $sessions = [];

    public function nextIdentity(): SessionId
    {
        return SessionId::fromString(Uuid::uuid4()->toString());
    }

    public function save(Session $session): void
    {
        $this->sessions[$session->sessionId()->asString()] = $session;
    }

    public function getById(SessionId $sessionId): Session
    {
        if (!isset($this->sessions[$sessionId->asString()])) {
            throw new RuntimeException('Could not find session with ID ' . $sessionId->asString());
        }

        return $this->sessions[$sessionId->asString()];
    }
}
