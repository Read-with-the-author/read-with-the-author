<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;

final class AccessPolicy
{
    /**
     * @var Application
     */
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function whenMemberRequestedAccess(MemberRequestedAccess $event): void
    {
        // TODO ask LeanpubSales service
        $this->application->grantAccess($event->memberId());
    }
}
