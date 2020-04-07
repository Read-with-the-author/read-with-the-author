<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Doctrine\DBAL\Connection as DbalConnection;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Application\Email\Mailer;
use LeanpubBookClub\Application\Members\Members;
use LeanpubBookClub\Application\UpcomingSessions\Sessions;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Domain\Model\Session\SessionRepository;
use LeanpubBookClub\Infrastructure\Doctrine\Connection;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchaseFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;
use LeanpubBookClub\Infrastructure\TalisOrm\EventDispatcherAdapter;
use LeanpubBookClub\Infrastructure\TalisOrm\MembersUsingDoctrineDbal;
use LeanpubBookClub\Infrastructure\TalisOrm\MemberTalisOrmRepository;
use LeanpubBookClub\Infrastructure\TalisOrm\PurchaseTalisOrmRepository;
use LeanpubBookClub\Infrastructure\TalisOrm\SessionsUsingDoctrineDbal;
use LeanpubBookClub\Infrastructure\TalisOrm\SessionTalisOrmRepository;
use TalisOrm\AggregateRepository;

final class ProductionServiceContainer extends ServiceContainer
{
    private Configuration $configuration;

    private DbalConnection $dbalConnection;

    private Mailer $mailer;

    public function __construct(Configuration $configuration, DbalConnection $connection, Mailer $mailer)
    {
        $this->configuration = $configuration;
        $this->dbalConnection = $connection;
        $this->mailer = $mailer;
    }

    protected function mailer(): Mailer
    {
        return $this->mailer;
    }

    protected function clock(): Clock
    {
        return new SystemClock();
    }

    protected function authorTimeZone(): TimeZone
    {
        return $this->configuration->authorTimeZone();
    }

    protected function purchaseRepository(): PurchaseRepository
    {
        return new PurchaseTalisOrmRepository($this->talisOrmAggregateRepository());
    }

    protected function memberRepository(): MemberRepository
    {
        return new MemberTalisOrmRepository($this->talisOrmAggregateRepository());
    }

    protected function individualPurchases(): IndividualPurchases
    {
        return new IndividualPurchaseFromLeanpubApi(
            $this->configuration->leanpubBookSlug(),
            $this->configuration->leanpubApiKey()
        );
    }

    public function setApplication(ApplicationInterface $application): void
    {
        $this->application = $application;
    }

    private function talisOrmAggregateRepository(): AggregateRepository
    {
        return new AggregateRepository(
            $this->dbalConnection,
            new EventDispatcherAdapter($this->eventDispatcher())
        );
    }

    protected function members(): Members
    {
        return new MembersUsingDoctrineDbal($this->connection());
    }

    protected function sessionRepository(): SessionRepository
    {
        return new SessionTalisOrmRepository($this->talisOrmAggregateRepository());
    }

    private function connection(): Connection
    {
        return new Connection($this->dbalConnection);
    }

    protected function sessions(): Sessions
    {
        return new SessionsUsingDoctrineDbal($this->connection());
    }
}
