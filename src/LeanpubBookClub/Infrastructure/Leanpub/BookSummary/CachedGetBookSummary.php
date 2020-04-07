<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Leanpub\BookSlug;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedGetBookSummary implements GetBookSummary
{
    private GetBookSummary $getBookSummary;

    private CacheInterface $bookSummaryCache;

    public function __construct(GetBookSummary $getBookSummary, CacheInterface $bookSummaryCache)
    {
        $this->getBookSummary = $getBookSummary;
        $this->bookSummaryCache = $bookSummaryCache;
    }

    public function getBookSummary(BookSlug $leanpubBookSlug): BookSummary
    {
        return $this->bookSummaryCache->get($leanpubBookSlug->asString(), function (ItemInterface $item) use ($leanpubBookSlug) {
            $item->expiresAfter(null); // never expire, a re-deploy will refresh the information

            return $this->getBookSummary->getBookSummary($leanpubBookSlug);
        });
    }
}
