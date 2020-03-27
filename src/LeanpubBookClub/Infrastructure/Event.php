<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure;

final class Event
{
    public static function asString(object $event): string
    {
        if (method_exists($event, '__toString')) {
            return (string)$event;
        }

        return get_class($event);
    }
}
