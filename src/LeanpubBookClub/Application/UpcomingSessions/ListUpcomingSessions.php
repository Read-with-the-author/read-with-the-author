<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\UpcomingSessions;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Member\MemberId;

interface ListUpcomingSessions
{
    /**
     * @return array<UpcomingSession>
     */
    public function upcomingSessions(DateTimeImmutable $currentTime, MemberId $activeMemberId): array;
}