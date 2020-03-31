<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\Entity;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;

final class Member
{
    use Entity;

    private LeanpubInvoiceId $memberId;

    private EmailAddress $emailAddress;

    private ?AccessToken $accessToken = null;

    private TimeZone $timeZone;

    private function __construct(
        LeanpubInvoiceId $leanpubInvoiceId,
        EmailAddress $emailAddress,
        TimeZone $timeZone
    ) {
        $this->memberId = $leanpubInvoiceId;
        $this->emailAddress = $emailAddress;
        $this->timeZone = $timeZone;
    }

    public static function requestAccess(
        LeanpubInvoiceId $leanpubInvoiceId,
        EmailAddress $emailAddress,
        TimeZone $timeZone
    ): self {
        $member = new self($leanpubInvoiceId, $emailAddress, $timeZone);

        $member->events[] = new MemberRequestedAccess($leanpubInvoiceId, $emailAddress, $timeZone);

        return $member;
    }

    public function grantAccess(): void
    {
        $this->events[] = new AccessWasGrantedToMember($this->memberId, $this->emailAddress);
    }

    public function memberId(): LeanpubInvoiceId
    {
        return $this->memberId;
    }

    public function generateAccessToken(AccessTokenGenerator $accessTokenGenerator): void
    {
        $this->accessToken = $accessTokenGenerator->generate();

        $this->events[] = new AnAccessTokenWasGenerated($this->memberId, $this->emailAddress, $this->accessToken);
    }
}
