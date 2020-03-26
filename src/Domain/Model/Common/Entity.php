<?php
declare(strict_types=1);

namespace LeanpubBookClub\Domain\Model\Common;

trait Entity
{
    /**
     * @var array<object>
     */
    private array $events = [];

    public function releaseEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}
