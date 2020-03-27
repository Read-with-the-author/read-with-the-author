<?php
declare(strict_types=1);

namespace Test\Acceptance;

final class EventDispatcherSpy
{
    /**
     * @var array<object>
     */
    private array $dispatchedEvents = [];

    public function notify(object $event): void
    {
        $this->printEventOnSingleLine($event);

        $this->dispatchedEvents[] = $event;
    }

    /**
     * @return array<object>
     */
    public function dispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }

    private function printEventOnSingleLine(object $event): void
    {
        echo $this->eventAsString($event) . "\n";
    }

    private function eventAsString(object $event): string
    {
        if (method_exists($event, '__toString')) {
            return (string)$event;
        }

        return get_class($event);
    }

    public function clearEvents(): void
    {
        $this->dispatchedEvents = [];
    }
}
