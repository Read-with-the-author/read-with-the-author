<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

final class Member
{
    private EmailAddress $emailAddress;

    private MemberId $memberId;

    private LeanpubInvoiceId $leanpubInvoiceId;

    /**
     * @var array<object>
     */
    private array $events = [];

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

    public function memberId(): MemberId
    {
        return $this->memberId;
    }

    public function releaseEvents(): array
    {
        return $this->events;
    }
}
