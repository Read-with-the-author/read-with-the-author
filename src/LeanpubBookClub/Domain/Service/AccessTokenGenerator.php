<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Service;

use LeanpubBookClub\Domain\Model\Member\AccessToken;

interface AccessTokenGenerator
{
    public function generate(): AccessToken;
}
