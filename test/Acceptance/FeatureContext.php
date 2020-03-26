<?php
declare(strict_types=1);

namespace Test\Acceptance;

use Behat\Behat\Context\Context;
use LeanpubBookClub\Application\Application;

abstract class FeatureContext implements Context
{
    /**
     * @var ServiceContainerForAcceptanceTesting
     */
    private ServiceContainerForAcceptanceTesting $serviceContainer;

    public function __construct()
    {
        $this->serviceContainer = new ServiceContainerForAcceptanceTesting();
    }

    /**
     * @return array<object>
     */
    protected function dispatchedEvents(): array
    {
        return $this->serviceContainer->eventDispatcher()->dispatchedEvents();
    }

    protected function clearEvents(): void
    {
        $this->serviceContainer->eventDispatcher()->clearEvents();
    }

    protected function application(): Application
    {
        return $this->serviceContainer->application();
    }

    protected function serviceContainer(): ServiceContainerForAcceptanceTesting
    {
        return $this->serviceContainer;
    }
}
