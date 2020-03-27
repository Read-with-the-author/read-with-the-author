<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

final class EventDispatcherWithSubscribers implements EventDispatcher
{
    /**
     * @var array<string,array<int,callable>>
     */
    private array $subscribersForEvent;


    /**
     * @var array<int,callable>
     */
    private array $genericSubscribers = [];

    public function subscribeToSpecificEvent(string $eventType, callable $subscriber): void
    {
        $this->subscribersForEvent[$eventType][] = $subscriber;
    }

    public function subscribeToAllEvents(callable $subscriber): void
    {
        $this->genericSubscribers[] = $subscriber;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->genericSubscribers as $subscriber) {
            $subscriber($event);
        }

        foreach ($this->subscribersForEvent[get_class($event)] ?? [] as $subscriber) {
            $subscriber($event);
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
