<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;

final class MemberRepositoryInMemory implements MemberRepository
{
    /**
     * @var array<string,Member>
     */
    private array $members = [];

    public function save(Member $member): void
    {
        $this->members[$member->memberId()->asString()] = $member;
    }

    public function getById(LeanpubInvoiceId $memberId): Member
    {
        if (!isset($this->members[$memberId->asString()])) {
            throw CouldNotFindMember::withId($memberId);
        }

        return $this->members[$memberId->asString()];
    }

    public function exists(LeanpubInvoiceId $memberId): bool
    {
        return isset($this->members[$memberId->asString()]);
    }
}
