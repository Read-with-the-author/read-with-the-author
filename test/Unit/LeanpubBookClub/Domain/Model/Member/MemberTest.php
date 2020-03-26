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
        $memberId = $this->aNewMemberId();
        $emailAddress = $this->anEmailAddress();
        $leanpubInvoiceId = $this->aLeanpubInvoiceId();

        $member = Member::requestAccess($memberId, $emailAddress, $leanpubInvoiceId);

        self::assertEquals(
            [
                new MemberRequestedAccess($memberId, $emailAddress, $leanpubInvoiceId)
            ],
            $member->releaseEvents()
        );

        self::assertEquals($memberId, $member->memberId());
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

    private function aNewMemberId(): MemberId
    {
        return MemberId::fromString('d3ab365c-b594-4f49-8fd0-bb0bfa584703');
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
        return Member::requestAccess($this->aNewMemberId(), $this->anEmailAddress(), $this->aLeanpubInvoiceId());
    }
}
