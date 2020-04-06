<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\ProducesFlashMessage;

final class AccessTokenEmailWasSent implements ProducesFlashMessage
{
    public function flashType(): string
    {
        return FlashType::SUCCESS;
    }

    public function flashTranslatableMessage(): string
    {
        return 'access_token_email_was_sent.flash_message';
    }

    public function flashTranslationVariables(): array
    {
        return [];
    }
}
