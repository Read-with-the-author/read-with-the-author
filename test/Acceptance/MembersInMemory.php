<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\AnAccessTokenWasGenerated;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;
use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;

final class MembersInMemory implements Members
{
    /**
     * @var array<string,Member>
     */
    private array $members = [];

    /**
     * @var array<string,string>
     */
    private array $memberIdsByAccessToken = [];

    public function whenMemberRequestedAccess(MemberRequestedAccess $event): void
    {
        $this->members[$event->leanpubInvoiceId()->asString()] = new Member(
            $event->leanpubInvoiceId()->asString(),
            $event->memberTimeZone()->asString(),
            $event->emailAddress()->asString()
        );
    }

    public function whenAnAccessTokenWasGenerated(AnAccessTokenWasGenerated $event): void
    {
        $this->memberIdsByAccessToken[$event->accessToken()->asString()] = $event->memberId()->asString();
    }

    public function getOneByAccessToken(AccessToken $accessToken): Member
    {
        if (!isset($this->memberIdsByAccessToken[$accessToken->asString()])) {
            throw CouldNotFindMember::withAccessToken($accessToken);
        }

        return $this->getOneById(LeanpubInvoiceId::fromString($this->memberIdsByAccessToken[$accessToken->asString()]));
    }

    public function getOneById(LeanpubInvoiceId $memberId): Member
    {
        if (!isset($this->members[$memberId->asString()])) {
            throw CouldNotFindMember::withId($memberId);
        }

        return $this->members[$memberId->asString()];
    }

    public function listMembers(): array
    {
        return [];
    }
}
