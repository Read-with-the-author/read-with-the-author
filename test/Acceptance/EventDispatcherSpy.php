<?php
declare(strict_types=1);

namespace Test\Acceptance;

use LeanpubBookClub\Application\EventDispatcher;

final class EventDispatcherSpy implements EventDispatcher
{
    /**
     * @var array<object>
     */
    private array $dispatchedEvents;

    private EventDispatcher $wrappedEventDispatcher;

    public function __construct(EventDispatcher $wrappedEventDispatcher)
    {
        $this->wrappedEventDispatcher = $wrappedEventDispatcher;
    }

    public function dispatch(object $event): void
    {
        $this->wrappedEventDispatcher->dispatch($event);

        $this->dispatchedEvents[] = $event;
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    /**
     * @return array<object>
     */
    public function dispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}
