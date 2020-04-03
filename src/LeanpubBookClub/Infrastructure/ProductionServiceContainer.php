<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Doctrine\DBAL\Connection;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AssetPublisher;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Application\Email\Mailer;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Domain\Model\Member\MemberRepository;
use LeanpubBookClub\Domain\Model\Purchase\PurchaseRepository;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummaryFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchaseFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;
use LeanpubBookClub\Infrastructure\TalisOrm\EventDispatcherAdapter;
use LeanpubBookClub\Infrastructure\TalisOrm\MemberTalisOrmRepository;
use LeanpubBookClub\Infrastructure\TalisOrm\PurchaseTalisOrmRepository;
use TalisOrm\AggregateRepository;

final class ProductionServiceContainer extends ServiceContainer
{
    private Configuration $configuration;

    private Connection $dbalConnection;

    public function __construct(Configuration $configuration, Connection $connection, Mailer $mailer)
    {
        $this->configuration = $configuration;
        $this->dbalConnection = $connection;
        $this->mailer = $mailer;
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

    protected function getBookSummary(): GetBookSummary
    {
        return new GetBookSummaryFromLeanpubApi(
            $this->configuration->leanpubBookSlug(),
            $this->configuration->leanpubApiKey()
        );
    }

    protected function assetPublisher(): AssetPublisher
    {
        return new PublicAssetPublisher($this->configuration->assetsDirectory());
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
}
