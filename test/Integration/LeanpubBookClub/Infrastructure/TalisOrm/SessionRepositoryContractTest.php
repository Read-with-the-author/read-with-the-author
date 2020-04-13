<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Generator;
use LeanpubBookClub\Domain\Model\Session\Session;
use LeanpubBookClub\Domain\Model\Session\SessionFactoryMethods;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Infrastructure\IntegrationTestServiceContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group repositories
 */
final class SessionRepositoryContractTest extends KernelTestCase
{
    use SessionFactoryMethods;

    /**
     * @test
     * @dataProvider sessions
     */
    public function it_can_save_and_get_a_session_by_its_id(Session $session, callable $updateFunction): void
    {
        $updateFunction($session);
        $this->sessionRepository()->save($session);

        self::assertEquals($session, $this->sessionRepository()->getById($session->sessionId()));
    }

    /**
     * @test
     * @dataProvider sessions
     */
    public function it_can_save_a_session_correctly_between_fetches_it_from_the_repository(
        Session $session,
        callable $updateFunction
    ): void {
        $this->sessionRepository()->save($session);

        $session = $this->sessionRepository()->getById($session->sessionId());
        $updateFunction($session);

        $this->sessionRepository()->save($session);

        self::assertEquals($session, $this->sessionRepository()->getById($session->sessionId()));
    }

    /**
     * @return Generator<array<int,Session|\Closure>>
     */
    public function sessions(): Generator
    {
        yield [
            $this->aSession(),
            function (Session $session): void {
                // change nothing
            }
        ];

        yield [
            $this->aSession(),
            function (Session $session): void {
                $session->attend($this->aMemberId());
                $session->attend($this->anotherMemberId());
            }
        ];

        $session = $this->aSession();
        $memberId = $this->aMemberId();
        $session->attend($memberId);
        yield [
            $this->aSession(),
            function (Session $session) use ($memberId): void {
                $session->attend($this->anotherMemberId());
                // Cancel the attendance of the second member
                $session->cancelAttendance($memberId);
            }
        ];

        yield [
            $this->aSession(),
            function (Session $session): void {
                $memberId = $this->aMemberId();
                $session->attend($memberId);
                $session->attend($this->anotherMemberId());
            }
        ];

        yield [
            $this->aSession(),
            function (Session $session): void {
                $session->setCallUrl($this->aUrlForTheCall());
            }
        ];

        yield [
            $this->aSession(),
            function (Session $session): void {
                $session->update($this->anUpdatedDescription(), $this->aUrlForTheCall());
            }
        ];

        yield [
            $this->aSession(),
            function (Session $session): void {
                $session->cancel();
            }
        ];
    }

    protected function tearDown(): void
    {
        $this->doctrineDbal()->executeQuery('DELETE FROM ' . Session::tableName() . ' WHERE 1');
    }

    private function sessionRepository(): SessionRepository
    {
        if (self::$container === null) {
            self::bootKernel();
        }

        $serviceContainer = self::$container->get(IntegrationTestServiceContainer::class);
        Assert::that($serviceContainer)->isInstanceOf(IntegrationTestServiceContainer::class);
        /** @var IntegrationTestServiceContainer $serviceContainer */

        return $serviceContainer->sessionRepository();
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
}
