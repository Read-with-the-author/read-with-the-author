<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindMember extends AbstractUserFacingError
{
    public static function withId(LeanpubInvoiceId $memberId): self
    {
        return new self(
            'leanpub_invoice_id.does_not_exist',
            [
                '{leanpubInvoiceId}' => $memberId->asString()
            ]
        );
    }

    public static function withAccessToken(AccessToken $accessToken): self
    {
        return new self(
            sprintf(
                'Could not find a member with access token %s',
                $accessToken->asString()
            )
        );
    }
}
