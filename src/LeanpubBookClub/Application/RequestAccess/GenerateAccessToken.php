<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application\RequestAccess;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Domain\Model\Member\AccessWasGrantedToMember;

final class GenerateAccessToken
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function whenAccessWasGrantedToMember(AccessWasGrantedToMember $event): void
    {
        $this->application->generateAccessToken($event->leanpubInvoiceId());
    }
}
