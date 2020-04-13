<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotGenerateAccessToken extends AbstractUserFacingError
{
    public static function becauseMemberHasNotBeenGrantedAccessYet(LeanpubInvoiceId $memberId): self
    {
        return new self(
            'could_not_generate_access_token.because_member_has_not_been_granted_access_yet',
            [
                'memberId' => $memberId->asString()
            ]
        );
    }
}
