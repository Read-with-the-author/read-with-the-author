<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\TalisOrm;

use LeanpubBookClub\Application\EventDispatcher;
use TalisOrm\DomainEvents\EventDispatcher as TalisOrmEventDispatcher;

final class EventDispatcherAdapter implements TalisOrmEventDispatcher
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array<object> $events
     */
    public function dispatch(array $events): void
    {
        $this->eventDispatcher->dispatchAll($events);
    }
}
