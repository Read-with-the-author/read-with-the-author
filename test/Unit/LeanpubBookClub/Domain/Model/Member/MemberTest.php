<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\EntityTestCase;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;

final class MemberTest extends EntityTestCase
{
    /**
     * @test
     */
    public function they_can_request_access(): void
    {
        $emailAddress = $this->anEmailAddress();
        $leanpubInvoiceId = $this->aLeanpubInvoiceId();

        $member = Member::requestAccess($leanpubInvoiceId, $emailAddress);

        self::assertEquals(
            [
                new MemberRequestedAccess($leanpubInvoiceId, $emailAddress)
            ],
            $member->releaseEvents()
        );

        self::assertEquals($leanpubInvoiceId, $member->memberId());
    }

    /**
     * @test
     */
    public function they_can_be_granted_access(): void
    {
        $member = $this->aMember();

        $member->grantAccess($this->accessTokenGenerator());

        self::assertArrayContainsObjectOfType(AccessGrantedToMember::class, $member->releaseEvents());
    }

    /**
     * @test
     */
    public function when_granted_access_an_access_token_will_be_generated(): void
    {
        $member = $this->aMember();

        $accessToken = AccessToken::fromString('e47755b7-5828-4d34-8471-f41967881312');

        $member->grantAccess($this->accessTokenGenerator($accessToken));

        self::assertContainsEquals(
            new AnAccessTokenWasGenerated($member->memberId(), $this->anEmailAddress(), $accessToken),
            $member->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function it_releases_events_only_once(): void
    {
        $member = $this->aMember();

        self::assertNotEmpty($member->releaseEvents());

        self::assertEmpty($member->releaseEvents());
    }

    private function anEmailAddress(): EmailAddress
    {
        return EmailAddress::fromString('info@matthiasnoback.nl');
    }

    private function aLeanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg');
    }

    private function aMember(): Member
    {
        return Member::requestAccess($this->aLeanpubInvoiceId(), $this->anEmailAddress());
    }

    private function accessTokenGenerator(?AccessToken $accessToken = null): AccessTokenGenerator
    {
        $accessToken = $accessToken ?? AccessToken::fromString('e47755b7-5828-4d34-8471-f41967881312');

        $accessTokenGenerator = $this->createStub(AccessTokenGenerator::class);
        $accessTokenGenerator->method('generate')->willReturn($accessToken);

        return $accessTokenGenerator;
    }
}
