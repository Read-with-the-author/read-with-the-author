<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\TimeZone;

final class MemberTimeZoneChanged
{
    private LeanpubInvoiceId $memberId;

    private TimeZone $newTimeZone;

    public function __construct(LeanpubInvoiceId $memberId, TimeZone $newTimeZone)
    {
        $this->memberId = $memberId;
        $this->newTimeZone = $newTimeZone;
    }
}
