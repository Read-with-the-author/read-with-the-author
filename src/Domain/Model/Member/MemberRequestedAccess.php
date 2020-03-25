<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

final class MemberRequestedAccess
{
    private EmailAddress $emailAddress;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private MemberId $memberId;

    public function __construct(MemberId $memberId, EmailAddress $emailAddress, LeanpubInvoiceId $leanpubInvoiceId)
    {
        $this->memberId = $memberId;
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public function memberId(): MemberId
    {
        return $this->memberId;
    }

    public function leanpubInvoiceId(): LeanpubInvoiceId
    {
        return $this->leanpubInvoiceId;
    }
}
