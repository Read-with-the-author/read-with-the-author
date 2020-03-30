<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Email;

use LeanpubBookClub\Application\EventProducesFlashMessage;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;

final class AccessTokenEmailWasSent implements EventProducesFlashMessage
{
    private EmailAddress $emailAddress;

    public function __construct(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

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
        return [
            '{emailAddress}' => $this->emailAddress->asString()
        ];
    }
}
