<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

interface MemberRepository
{
    public function save(Member $member): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getById(LeanpubInvoiceId $memberId): Member;

    public function exists(LeanpubInvoiceId $memberId): bool;
}
