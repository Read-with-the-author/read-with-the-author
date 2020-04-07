<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Command;

use LeanpubBookClub\Application\AssetPublisher;
use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RefreshBookInformation extends Command
{
    private GetBookSummary $getBookSummary;

    private AssetPublisher $assetPublisher;

    private BookSlug $leanpubBookSlug;

    public function __construct(GetBookSummary $getBookSummary, AssetPublisher $assetPublisher, BookSlug $leanpubBookSlug)
    {
        parent::__construct();

        $this->getBookSummary = $getBookSummary;
        $this->assetPublisher = $assetPublisher;
        $this->leanpubBookSlug = $leanpubBookSlug;
    }

    protected function configure()
    {
        $this->setName('leanpub:refresh-book-information');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Refreshing book information...');

        $bookSummary = $this->getBookSummary->getBookSummary($this->leanpubBookSlug);
        $this->assetPublisher->publishTitlePageImageUrl($bookSummary->titlePageUrl());

        $output->writeln('Done');

        return 0;
    }
}
