<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;

interface GetBookSummary
{
    public function getBookSummary(BookSlug $leanpubBookSlug): BookSummary;
}
