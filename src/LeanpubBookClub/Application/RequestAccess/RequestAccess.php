<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\RequestAccess;

use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class RequestAccess
{
    private string $emailAddress;

    private string $leanpubInvoiceId;

    public function __construct(string $leanpubInvoiceId, string $emailAddress)
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
