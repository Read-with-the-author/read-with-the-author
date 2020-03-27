<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Leanpub\BookSummary;

use LeanpubBookClub\Infrastructure\Leanpub\ExtractFromDecodedData;

final class BookSummary
{
    use ExtractFromDecodedData;

    private string $title;

    private string $subtitle;

    private string $authorString;

    private string $titlePageUrl;

    private string $url;

    public function __construct(
        string $title,
        string $subtitle,
        string $authorString,
        string $titlePageUrl,
        string $url
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->authorString = $authorString;
        $this->titlePageUrl = $titlePageUrl;
        $this->url = $url;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromJsonDecodedData(array $data): self
    {
        return new self(
            self::extractString($data, 'title'),
            self::extractString($data, 'subtitle'),
            self::extractString($data, 'author_string'),
            self::extractString($data, 'title_page_url'),
            self::extractString($data, 'url')
        );
    }
}
