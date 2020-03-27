<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

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

    public static function createFromEnvironmentVariables(): self
    {
        return new self(
            Configuration::createFromEnvironmentVariables()
        );
    }

    protected function individualPurchases(): IndividualPurchases
    {
        return new IndividualPurchaseFromLeanpubApi(
            $this->configuration->leanpubBookSlug(),
            $this->configuration->leanpubApiKey()
        );
    }

    private function getBookSummary(): GetBookSummary
    {
        return new GetBookSummaryFromLeanpubApi(
            $this->configuration->leanpubBookSlug(),
            $this->configuration->leanpubApiKey()
        );
    }
}
