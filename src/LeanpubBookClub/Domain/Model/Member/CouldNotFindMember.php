<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use RuntimeException;

final class CouldNotFindMember extends RuntimeException
{
    public static function withId(LeanpubInvoiceId $memberId): self
    {
        return new self(
            sprintf(
                'Could not find member with ID %s',
                $memberId->asString()
            )
        );
    }

    public static function withAccessToken(string $accessToken): self
    {
        return new self(
            sprintf(
                'Could not find a member with access token %s',
                $accessToken
            )
        );
    }
}
