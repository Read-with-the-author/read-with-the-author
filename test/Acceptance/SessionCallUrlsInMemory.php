<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\SessionCall\CouldNotGetCallUrl;
use LeanpubBookClub\Application\SessionCall\SessionCallUrls;
use LeanpubBookClub\Domain\Model\Session\SessionId;
use LeanpubBookClub\Domain\Model\Session\UrlForCallWasUpdated;

final class SessionCallUrlsInMemory implements SessionCallUrls
{
    /**
     * @var array<string,string>
     */
    private array $urls = [];

    public function whenUrlForCallWasProvided(UrlForCallWasUpdated $event): void
    {
        $this->urls[$event->sessionId()->asString()] = $event->callUrl();
    }

    public function getCallUrlForSession(SessionId $sessionId): string
    {
        if (!isset($this->urls[$sessionId->asString()])) {
            throw CouldNotGetCallUrl::forSession($sessionId);
        }

        return $this->urls[$sessionId->asString()];
    }
}
