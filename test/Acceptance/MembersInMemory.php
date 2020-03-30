<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

final class MembersInMemory implements Members
{
    public function getOneByAccessToken(string $accessToken): Member
    {
        throw CouldNotFindMember::withAccessToken($accessToken);
    }

    public function getOneById(string $memberId): Member
    {
        throw CouldNotFindMember::withId(LeanpubInvoiceId::fromString($memberId));
    }
}
