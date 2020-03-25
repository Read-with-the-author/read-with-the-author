<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberId;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use Ramsey\Uuid\Uuid;

final class MemberRepositoryInMemory implements MemberRepository
{
    /**
     * @var array<string,Member>
     */
    private array $members = [];

    public function nextIdentity(): MemberId
    {
        return MemberId::fromString(Uuid::uuid4()->toString());
    }

    public function save(Member $member): void
    {
        $this->members[$member->memberId()->asString()] = $member;
    }
}
