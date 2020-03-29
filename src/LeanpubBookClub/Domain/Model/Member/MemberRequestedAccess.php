<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\EmailAddress;

final class MemberRequestedAccess
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
}
