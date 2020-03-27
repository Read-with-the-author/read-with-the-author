<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

interface AssetPublisher
{
    public function publishTitlePageImageUrl(string $titlePageUrl): void;
}
