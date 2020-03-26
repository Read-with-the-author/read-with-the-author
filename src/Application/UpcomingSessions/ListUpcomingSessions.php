<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;

interface ListUpcomingSessions
{
    /**
     * @return array<UpcomingSession>
     */
    public function upcomingSessions(DateTimeImmutable $currentTime): array;
}
