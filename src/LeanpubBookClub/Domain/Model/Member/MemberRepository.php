<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use RuntimeException;

interface MemberRepository
{
    public function save(Member $member): void;

    /**
     * @throws RuntimeException
     */
    public function getById(LeanpubInvoiceId $memberId): Member;
}
