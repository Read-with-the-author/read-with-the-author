<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use DateTimeImmutable;
use LeanpubBookClub\Application\ProducesFlashMessage;
use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class MemberRequestedAccess implements ProducesFlashMessage
{
    private EmailAddress $emailAddress;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private TimeZone $memberTimeZone;

    private DateTimeImmutable $requestedAt;

    public function __construct(
        LeanpubInvoiceId $memberId,
        EmailAddress $emailAddress,
        TimeZone $memberTimeZone,
        DateTimeImmutable $requestedAt
    ) {
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $memberId;
        $this->memberTimeZone = $memberTimeZone;
        $this->requestedAt = $requestedAt;
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
