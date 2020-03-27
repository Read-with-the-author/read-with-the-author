<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\EmailAddress;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class RequestAccess
{
    private string $emailAddress;

    private string $leanpubInvoiceId;

    public function __construct(string $emailAddress, string $leanpubInvoiceId)
    {
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public function emailAddress(): EmailAddress
    {
        return EmailAddress::fromString($this->emailAddress);
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString($this->leanpubInvoiceId);
    }
}
