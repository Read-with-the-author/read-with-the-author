<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchaseFromLeanpubApi;
use LeanpubBookClub\Infrastructure\Leanpub\IndividualPurchases;

final class ProductionServiceContainer extends ServiceContainer
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
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
            $this->configuration->leanpubApiKey(),
            $this->purchaseFactory()
        );
    }
}
