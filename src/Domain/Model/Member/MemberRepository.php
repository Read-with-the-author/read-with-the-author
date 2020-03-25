<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use RuntimeException;

interface MemberRepository
{
    public function nextIdentity(): MemberId;

    public function save(Member $member): void;

    /**
     * @throws RuntimeException
     */
    public function getById(MemberId $memberId): Member;
}
