<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;

final class Member
{
    use Entity;

    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private ?AccessToken $accessToken = null;

    private function __construct(LeanpubInvoiceId $leanpubInvoiceId, EmailAddress $emailAddress)
    {
        $this->memberId = $leanpubInvoiceId;
        $this->emailAddress = $emailAddress;
    }

    public static function requestAccess(LeanpubInvoiceId $leanpubInvoiceId, EmailAddress $emailAddress): self
    {
        $member = new self($leanpubInvoiceId, $emailAddress);

        $member->events[] = new MemberRequestedAccess($leanpubInvoiceId, $emailAddress);

        return $member;
    }

    public function grantAccess(AccessTokenGenerator $accessTokenGenerator): void
    {
        $this->events[] = new AccessGrantedToMember($this->memberId, $this->emailAddress);

        $this->generateAccessToken($accessTokenGenerator);
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }

    private function generateAccessToken(AccessTokenGenerator $accessTokenGenerator): void
    {
        $this->accessToken = $accessTokenGenerator->generate();

        $this->events[] = new AnAccessTokenWasGenerated($this->memberId, $this->emailAddress, $this->accessToken);
    }
}
