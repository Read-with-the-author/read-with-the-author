<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Application\EventProducesFlashMessage;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;

final class MemberRequestedAccess implements EventProducesFlashMessage
{
    private EmailAddress $emailAddress;

    private LeanpubInvoiceId $leanpubInvoiceId;

    public function __construct(LeanpubInvoiceId $memberId, EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $memberId;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }

    public function flashType(): string
    {
        return FlashType::SUCCESS;
    }

    public function flashTranslatableMessage(): string
    {
        return 'member_requested_access.flash_message';
    }

    public function flashTranslationVariables(): array
    {
        return [
            '{emailAddress}' => $this->emailAddress->asString()
        ];
    }
}
