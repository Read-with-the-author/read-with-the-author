<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use DateTimeImmutable;

interface Clock
{
    public function currentTime(): DateTimeImmutable;
}
