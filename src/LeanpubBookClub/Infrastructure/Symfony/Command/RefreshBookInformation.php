<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Command;

use LeanpubBookClub\Application\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RefreshBookInformation extends Command
{
    private Application $application;

    public function __construct(Application $application)
    {
        parent::__construct();

        $this->application = $application;
    }

    protected function configure()
    {
        $this->setName('leanpub:refresh-book-information');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->application->refreshBookInformation();

        return 0;
    }
}
