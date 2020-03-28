<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\EntityTestCase;

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

        $member->grantAccess();

        self::assertArrayContainsObjectOfType(AccessGrantedToMember::class, $member->releaseEvents());
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
}
