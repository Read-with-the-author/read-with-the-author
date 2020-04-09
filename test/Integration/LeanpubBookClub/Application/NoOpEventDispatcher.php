<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

final class NoOpEventDispatcher implements EventDispatcher
{
    public function dispatch(object $event): void
    {
        // do nothing
    }

    public function dispatchAll(array $events): void
    {
        // do nothing
    }
}
