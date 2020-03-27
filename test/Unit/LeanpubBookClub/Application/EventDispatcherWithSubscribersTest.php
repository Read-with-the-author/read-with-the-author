<?php
declare(strict_types=1);

namespace LeanpubBookClub\Application;

use PHPUnit\Framework\TestCase;

final class EventDispatcherWithSubscribersTest extends TestCase
{
    /**
     * @test
     */
    public function it_notifies_subscribers_about_specific_types_of_events(): void
    {
        $events = [];

        $subscriber1WasCalled = false;
        $subscriber1 = function ($event) use (&$subscriber1WasCalled, &$events) {
            $events[] = $event;
            $subscriber1WasCalled = true;
        };

        $subscriber2WasCalled = false;
        $subscriber2 = function ($event) use (&$subscriber2WasCalled, &$events) {
            $events[] = $event;
            $subscriber2WasCalled = true;
        };

        $subscriber3WasCalled = false;
        $subscriber3 = function ($event) use (&$subscriber3WasCalled, &$events) {
            $events[] = $event;
            $subscriber3WasCalled = true;
        };

        $genericSubscriberWasCalled = false;
        $genericSubscriber = function ($event) use (&$genericSubscriberWasCalled, &$events) {
            $events[] = $event;
            $genericSubscriberWasCalled = true;
        };

        $eventDispatcher = new EventDispatcherWithSubscribers();
        $eventDispatcher->subscribeToSpecificEvent(DummyEvent::class, $subscriber1);
        $eventDispatcher->subscribeToSpecificEvent(DummyEvent::class, $subscriber2);
        $eventDispatcher->subscribeToSpecificEvent(AnotherDummyEvent::class, $subscriber3);
        $eventDispatcher->subscribeToAllEvents($genericSubscriber);

        $event = new DummyEvent();
        $eventDispatcher->dispatch($event);

        self::assertTrue($subscriber1WasCalled);
        self::assertTrue($subscriber2WasCalled);
        self::assertFalse($subscriber3WasCalled);
        self::assertTrue($genericSubscriberWasCalled);
        self::assertEquals([$event, $event, $event], $events);
    }
}
