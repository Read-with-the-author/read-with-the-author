<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\Entity;

final class Member
{
    use Entity;

    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private function __construct(LeanpubInvoiceId $leanpubInvoiceId, EmailAddress $emailAddress)
    {
        $this->memberId = $leanpubInvoiceId;
        $this->emailAddress = $emailAddress;
    }

    public static function requestAccess(LeanpubInvoiceId $leanpubInvoiceId, EmailAddress $emailAddress): self
    {
        $member = new self($leanpubInvoiceId, $emailAddress);

        $member->events[] = new MemberRequestedAccess($leanpubInvoiceId, $emailAddress);

        return $member;
    }

    public function grantAccess(): void
    {
        $this->events[] = new AccessGrantedToMember($this->memberId, $this->emailAddress);
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }
}
