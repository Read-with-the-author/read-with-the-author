<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use RuntimeException;

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
            throw new RuntimeException('Could not find member with ID ' . $memberId->asString());
        }

        return $this->members[$memberId->asString()];
    }
}
