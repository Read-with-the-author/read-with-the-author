<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Mapping;

final class BookSummary
{
    use Mapping;

    private string $title;

    private string $subtitle;

    private string $authorString;

    private string $titlePageUrl;

    private string $url;

    private string $pdfPublishedUrl;

    private string $epubPublishedUrl;

    private string $mobiPublishedUrl;

    public function __construct(
        string $title,
        string $subtitle,
        string $authorString,
        string $titlePageUrl,
        string $url,
        string $pdfPublishedUrl,
        string $epubPublishedUrl,
        string $mobiPublishedUrl
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->authorString = $authorString;
        $this->titlePageUrl = $titlePageUrl;
        $this->url = $url;
        $this->pdfPublishedUrl = $pdfPublishedUrl;
        $this->epubPublishedUrl = $epubPublishedUrl;
        $this->mobiPublishedUrl = $mobiPublishedUrl;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromJsonDecodedData(array $data): self
    {
        return new self(
            self::asString($data, 'title'),
            self::asString($data, 'subtitle'),
            self::asString($data, 'author_string'),
            self::asString($data, 'title_page_url'),
            self::asString($data, 'url'),
            self::asString($data, 'pdf_published_url'),
            self::asString($data, 'epub_published_url'),
            self::asString($data, 'mobi_published_url')
        );
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subtitle(): string
    {
        return $this->subtitle;
    }

    public function author(): string
    {
        return $this->authorString;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function titlePageUrl(): string
    {
        return $this->titlePageUrl;
    }

    public function isAnyDownloadAvailable(): bool
    {
        if ($this->isPdfDownloadAvailable()) {
            return true;
        }

        if ($this->isEpubDownloadAvailable()) {
            return true;
        }

        if ($this->isMobiDownloadAvailable()) {
            return true;
        }

        return false;
    }

    public function pdfPublishedUrl(): string
    {
        return $this->pdfPublishedUrl;
    }

    public function isPdfDownloadAvailable(): bool
    {
        return $this->pdfPublishedUrl() !== '';
    }

    public function epubPublishedUrl(): string
    {
        return $this->epubPublishedUrl;
    }

    private function isEpubDownloadAvailable(): bool
    {
        return $this->epubPublishedUrl() !== '';
    }

    public function mobiPublishedUrl(): string
    {
        return $this->mobiPublishedUrl;
    }

    private function isMobiDownloadAvailable(): bool
    {
        return $this->mobiPublishedUrl() !== '';
    }
}
