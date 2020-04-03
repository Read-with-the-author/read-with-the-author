<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Session;

use LeanpubBookClub\Domain\Model\Common\Uuid;
use TalisOrm\AggregateId;

final class SessionId implements AggregateId
{
    use Uuid;

    public function __toString(): string
    {
        return $this->asString();
    }
}
