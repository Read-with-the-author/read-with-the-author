<?php
declare(strict_types=1);

namespace Test\Acceptance;

use BadMethodCallException;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\BookSummary;
use LeanpubBookClub\Infrastructure\Leanpub\BookSummary\GetBookSummary;

final class GetBookSummaryInMemory implements GetBookSummary
{
    public function getBookSummary(): BookSummary
    {
        throw new BadMethodCallException('Not implemented');
    }
}
