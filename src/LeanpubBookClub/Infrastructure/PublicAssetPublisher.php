<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

use Assert\Assert;
use LeanpubBookClub\Application\AssetPublisher;
use function Safe\file_put_contents;

final class PublicAssetPublisher implements AssetPublisher
{
    private string $assetsDirectory;

    public function __construct(string $assetsDirectory)
    {
        Assert::that($assetsDirectory)->directory();

        $this->assetsDirectory = $assetsDirectory;
    }

    public function publishTitlePageImageUrl(string $titlePageUrl): void
    {
        $data = file_get_contents($titlePageUrl);
        file_put_contents($this->assetsDirectory . '/title_page.jpg', $data);
    }
}
