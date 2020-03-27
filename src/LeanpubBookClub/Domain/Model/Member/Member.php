<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\Entity;

final class Member
{
    use Entity;

    private EmailAddress $emailAddress;

    private MemberId $memberId;

    private LeanpubInvoiceId $leanpubInvoiceId;

    private function __construct(MemberId $memberId, EmailAddress $emailAddress, LeanpubInvoiceId $leanpubInvoiceId)
    {
        $this->memberId = $memberId;
        $this->emailAddress = $emailAddress;
        $this->leanpubInvoiceId = $leanpubInvoiceId;
    }

    public static function requestAccess(MemberId $memberId, EmailAddress $emailAddress, LeanpubInvoiceId $leanpubInvoiceId): self
    {
        $member = new self($memberId, $emailAddress, $leanpubInvoiceId);

        $member->events[] = new MemberRequestedAccess($memberId, $emailAddress, $leanpubInvoiceId);

        return $member;
    }

    public function grantAccess(): void
    {
        $this->events[] = new AccessGrantedToMember($this->memberId, $this->emailAddress);
    }

    public function memberId(): MemberId
    {
        return $this->memberId;
    }
}