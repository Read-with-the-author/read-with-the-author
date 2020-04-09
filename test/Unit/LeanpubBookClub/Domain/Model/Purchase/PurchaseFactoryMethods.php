<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Purchase;

use LeanpubBookClub\Domain\Model\Member\LeanpubInvoiceId;

trait PurchaseFactoryMethods
{
    protected function aRandomLeanpubInvoiceId(): LeanpubInvoiceId
    {
        return LeanpubInvoiceId::fromString(substr(sha1((string)mt_rand()), 0, 22));
    }
}
