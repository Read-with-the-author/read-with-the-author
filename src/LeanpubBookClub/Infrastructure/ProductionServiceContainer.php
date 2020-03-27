<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Application\AssetPublisher;
use LeanpubBookClub\Application\Clock;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummaryFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchaseFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases\IndividualPurchases;

final class ProductionServiceContainer extends ServiceContainer
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    protected function clock(): Clock
    {
        return new SystemClock();
    }

    public static function createFromEnvironmentVariables(string $projectDirectory): self
    {
        return new self(
            Configuration::createFromEnvironmentVariables($projectDirectory)
        );
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

}
