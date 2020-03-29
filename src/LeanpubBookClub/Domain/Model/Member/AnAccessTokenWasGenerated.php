<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

final class AnAccessTokenWasGenerated
{
    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private AccessToken $accessToken;

    public function __construct(LeanpubInvoiceId $memberId, EmailAddress $emailAddress, AccessToken $accessToken)
    {
        $this->memberId = $memberId;
        $this->emailAddress = $emailAddress;
        $this->accessToken = $accessToken;
    }

    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function accessToken(): AccessToken
    {
        return $this->accessToken;
    }
}
