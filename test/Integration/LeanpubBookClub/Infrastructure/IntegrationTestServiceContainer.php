<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\EventDispatcher;
use LeanpubBookClub\Application\NoOpEventDispatcher;

final class IntegrationTestServiceContainer extends ProductionServiceContainer
{
    public function eventDispatcher(): EventDispatcher
    {
        return new NoOpEventDispatcher();
    }

    public function setApplication(ApplicationInterface $application): void
    {
        $this->application = $application;
    }
}
