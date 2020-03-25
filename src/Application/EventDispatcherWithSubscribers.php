<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

final class EventDispatcherWithSubscribers implements EventDispatcher
{
    /**
     * @var array<string,array<int,callable>>
     */
    private array $subscribers;

    public function addSubscriber(string $eventType, callable $subscriber): void
    {
        $this->subscribers[$eventType][] = $subscriber;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->subscribers[get_class($event)] ?? [] as $subscriber) {
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
