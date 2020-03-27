<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\AssetPublisher;

final class AssetPublisherInMemory implements AssetPublisher
{
    public function publishTitlePageImageUrl(string $titlePageUrl)
    {
        // noop
    }
}
