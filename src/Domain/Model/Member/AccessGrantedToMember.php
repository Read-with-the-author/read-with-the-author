<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

final class AccessGrantedToMember
{
    private MemberId $memberId;

    private EmailAddress $emailAddress;

    public function __construct(MemberId $memberId, EmailAddress $emailAddress)
    {
        $this->memberId = $memberId;
        $this->emailAddress = $emailAddress;
    }

    public function memberId(): MemberId
    {
        return $this->memberId;
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }
}
