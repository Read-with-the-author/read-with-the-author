<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class UpdateTimeZone
{
    private string $memberId;

    private string $timeZone;

    public function __construct(string $memberId, string $timeZone)
    {
        $this->memberId = $memberId;
        $this->timeZone = $timeZone;
    }

    public function memberId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString($this->memberId);
    }

    public function timeZone(): TimeZone
    {
        return TimeZone::fromString($this->timeZone);
    }
}
