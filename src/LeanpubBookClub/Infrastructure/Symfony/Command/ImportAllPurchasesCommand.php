<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Command;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\EventDispatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportAllPurchasesCommand extends Command
{
    private ApplicationInterface $application;

    private EventDispatcher $eventDispatcher;

    public function __construct(ApplicationInterface $application, EventDispatcher $eventDispatcher)
    {
        parent::__construct();

        $this->application = $application;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        $this
            ->setName('leanpub:import-all-purchases')
            ->setAliases(['import'])
            ->addOption('loop', null, InputOption::VALUE_NONE, 'Keep importing')
            ->addOption('delay', null, InputOption::VALUE_REQUIRED, 'Sleep n seconds between imports', 60);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keepRunning = $input->hasOption('loop');

        $output->writeln(sprintf('Importing all purchases%s...', $keepRunning ? ' in a loop' : ''));
        $loop = new Loop(
            (int)$input->getOption('delay'),
            function () {
                $this->application->importAllPurchases();
            },
            $this->eventDispatcher
        );

        $loop->run($keepRunning);

        return 0;
    }
}
