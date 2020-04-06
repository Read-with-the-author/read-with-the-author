<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Application\FlashType;
use LeanpubBookClub\Application\ProducesFlashMessage;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class MemberTimeZoneChanged implements ProducesFlashMessage
{
    private LeanpubInvoiceId $memberId;

    private TimeZone $newTimeZone;

    public function __construct(LeanpubInvoiceId $memberId, TimeZone $newTimeZone)
    {
        $this->memberId = $memberId;
        $this->newTimeZone = $newTimeZone;
    }

    public function flashType(): string
    {
        return FlashType::SUCCESS;
    }

    public function flashTranslatableMessage(): string
    {
        return 'member_time_zone_changed.flash_message';
    }

    public function flashTranslationVariables(): array
    {
        return [];
    }
}
