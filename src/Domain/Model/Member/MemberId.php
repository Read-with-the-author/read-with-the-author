<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Member;

use LeanpubBookClub\Domain\Model\Common\EntityId;

final class MemberId
{
    use EntityId;

    public function equals(MemberId $other): bool
    {
        return $this->id === $other->id;
    }
}
