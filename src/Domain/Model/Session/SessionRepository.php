<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use RuntimeException;

interface SessionRepository
{
    /**
     * @throws RuntimeException
     */
    public function getById(SessionId $sessionId): Session;

    public function save(Session $session): void;

    public function nextIdentity(): SessionId;
}
