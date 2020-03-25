<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

interface MemberRepository
{
    public function nextIdentity(): MemberId;

    public function save(Member $member): void;
}
