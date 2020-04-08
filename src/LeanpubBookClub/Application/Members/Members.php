<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Members;

use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;
use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

interface Members
{
    /**
     * @throws CouldNotFindMember
     */
    public function getOneByAccessToken(AccessToken $accessToken): Member;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneById(LeanpubInvoiceId $memberId): Member;

    /**
     * @return array<MemberForAdministrator>
     */
    public function listMembers(): array;
}
