<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use LeanpubBookClub\Domain\Model\Member\MemberRequestedAccess;

final class AccessPolicy
{
    private Application $application;

    private LeanpubSales $leanpubSales;

    public function __construct(Application $application, LeanpubSales $leanpubSales)
    {
        $this->application = $application;
        $this->leanpubSales = $leanpubSales;
    }

    public function whenMemberRequestedAccess(MemberRequestedAccess $event): void
    {
        if ($this->leanpubSales->isInvoiceIdOfActualPurchase($event->leanpubInvoiceId())) {
            $this->application->grantAccess($event->memberId());
        }
    }
}
