<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Generator;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\AccessToken;
use LeanpubBookClub\Domain\Model\Member\Member;
use LeanpubBookClub\Domain\Model\Member\MemberFactoryMethods;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Service\AccessTokenGenerator;
use LeanpubBookClub\Infrastructure\IntegrationTestServiceContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group wip
 */
final class MemberRepositoryContractTest extends KernelTestCase
{
    use MemberFactoryMethods;

    /**
     * @test
     * @dataProvider members
     */
    public function it_can_save_and_get_a_member_by_its_id(Member $member): void
    {
        $this->memberRepository()->save($member);

        self::assertEquals($member, $this->memberRepository()->getById($member->memberId()));
    }

    /**
     * @return Generator<Member>
     */
    public function members(): Generator
    {
        $member = Member::requestAccess(
            $this->aRandomLeanpubInvoiceId(),
            $this->anEmailAddress(),
            $this->aTimeZone(),
            $this->now()
        );

        yield [$member];

        $member2 = Member::requestAccess(
            $this->aRandomLeanpubInvoiceId(),
            $this->anEmailAddress(),
            $this->aTimeZone(),
            $this->now()
        );
        $member2->grantAccess();

        yield [$member2];

        $member3 = Member::requestAccess(
            $this->aRandomLeanpubInvoiceId(),
            $this->anEmailAddress(),
            $this->aTimeZone(),
            $this->now()
        );
        $member3->generateAccessToken(
            $this->accessTokenGenerator()
        );

        yield [$member3];

        $member4 = Member::requestAccess(
            $this->aRandomLeanpubInvoiceId(),
            $this->anEmailAddress(),
            $this->aTimeZone(),
            $this->now()
        );
        $member4->grantAccess();
        $member4->generateAccessToken(
            $this->accessTokenGenerator()
        );
        $member4->changeTimeZone(TimeZone::fromString('America/New_York'));

        yield [$member4];
    }

    protected function tearDown(): void
    {
        $this->doctrineDbal()->executeQuery('DELETE FROM members');
    }

    private function memberRepository(): MemberRepository
    {
        if (self::$container === null) {
            self::bootKernel();
        }

        $serviceContainer = self::$container->get(IntegrationTestServiceContainer::class);
        Assert::that($serviceContainer)->isInstanceOf(IntegrationTestServiceContainer::class);
        /** @var IntegrationTestServiceContainer $serviceContainer */

        return $serviceContainer->memberRepository();
    }

    private function doctrineDbal(): Connection
    {
        if (self::$container === null) {
            self::bootKernel();
        }

        $connection = self::$container->get(Connection::class);
        Assert::that($connection)->isInstanceOf(Connection::class);
        /** @var Connection $connection */

        return $connection;
    }

    private function accessTokenGenerator(): AccessTokenGenerator
    {
        return new class implements AccessTokenGenerator {
            public function generate(): AccessToken
            {
                return AccessToken::fromString('863ab133-3240-4a53-8d5f-39f6a9d500b4');
            }
        };
    }
}
