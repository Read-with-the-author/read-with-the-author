<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

final class AccessGrantedToMember
{
    private LeanpubInvoiceId $leanpubInvoiceId;

    private EmailAddress $emailAddress;

    public function __construct(LeanpubInvoiceId $leanpubInvoiceId, EmailAddress $emailAddress)
    {
        $this->leanpubInvoiceId = $leanpubInvoiceId;
        $this->emailAddress = $emailAddress;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }
}
