<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Command;

use LeanpubBookClub\Application\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportAllPurchasesCommand extends Command
{
    private Application $application;

    public function __construct(Application $application)
    {
        parent::__construct();

        $this->application = $application;
    }

    protected function configure()
    {
        $this
            ->setName('leanpub:import-all-purchases')
            ->setAliases(['import']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->application->importAllPurchases();

        return 0;
    }
}
