<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use Ramsey\Uuid\Uuid;

final class RealUuidAccessTokenGenerator implements AccessTokenGenerator
{
    public function generate(): AccessToken
    {
        return AccessToken::fromString(Uuid::uuid4()->toString());
    }
}
