<?php

namespace ValidatedStatemachine\events;

use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatcherSingleton extends EventDispatcher
{
    protected static $eventDispatcher = null;

    /**
     * @return EventDispatcherSingleton
     */
    public static function getDispatcher()
    {
        if(is_null(self::$eventDispatcher)) {
            self::$eventDispatcher = new self;
            return self::$eventDispatcher;
        }

        return self::$eventDispatcher;
    }
}
