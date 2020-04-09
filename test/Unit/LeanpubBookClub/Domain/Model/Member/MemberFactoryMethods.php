<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use DateTimeImmutable;
use LeanpubBookClub\Domain\Model\Common\EmailAddress;
use LeanpubBookClub\Domain\Model\Common\TimeZone;

trait MemberFactoryMethods
{
    protected function aMember(): Member
    {
        return Member::requestAccess(
            $this->aLeanpubInvoiceId(),
            $this->anEmailAddress(),
            $this->aTimeZone(),
            $this->now()
        );
    }

    protected function aLeanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString('jP6LfQ3UkfOvZTLZLNfDfg');
    }

    protected function aRandomLeanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString(substr(sha1((string)mt_rand()), 0, 22));
    }

    protected function aTimeZone(): TimeZone
    {
        return TimeZone::fromString('Europe/Amsterdam');
    }

    protected function anEmailAddress(): EmailAddress
    {
        return EmailAddress::fromString('info@matthiasnoback.nl');
    }

    protected function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
