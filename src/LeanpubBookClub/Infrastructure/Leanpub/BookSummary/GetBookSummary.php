<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

interface GetBookSummary
{
    public function getBookSummary(): BookSummary;
}
