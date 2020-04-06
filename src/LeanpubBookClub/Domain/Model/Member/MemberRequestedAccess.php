<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Application\ProducesFlashMessage;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class MemberRequestedAccess implements ProducesFlashMessage
{
    private EmailAddress $emailAddress;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private TimeZone $memberTimeZone;

    public function __construct(LeanpubInvoiceId $memberId, EmailAddress $emailAddress, TimeZone $memberTimeZone)
    {
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $memberId;
        $this->memberTimeZone = $memberTimeZone;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }

    public function memberTimeZone(): TimeZone
    {
        return $this->memberTimeZone;
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
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
