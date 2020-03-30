<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\Members;

use LeanpubBookClub\Domain\Model\Member\CouldNotFindMember;

interface Members
{
    /**
     * @throws CouldNotFindMember
     */
    public function getOneByAccessToken(string $accessToken): Member;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneById(string $memberId): Member;
}
