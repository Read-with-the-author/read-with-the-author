<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Application\EventProducesFlashMessage;
use LeanpubBookClub\Application\FlashType;

final class SessionWasPlanned implements EventProducesFlashMessage
{
    private SessionId $sessionId;

    private ScheduledDate $date;

    private string $description;

    public function __construct(
        SessionId $sessionId,
        ScheduledDate $date,
        string $description
    ) {
        $this->sessionId = $sessionId;
        $this->date = $date;
        $this->description = $description;
    }

    public function sessionId(): SessionId
    {
        return $this->sessionId;
    }

    public function date(): ScheduledDate
    {
        return $this->date;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function flashType(): string
    {
        return FlashType::SUCCESS;
    }

    public function flashTranslatableMessage(): string
    {
        return 'A new session was planned';
    }

    public function flashTranslationVariables(): array
    {
        return [];
    }
}
