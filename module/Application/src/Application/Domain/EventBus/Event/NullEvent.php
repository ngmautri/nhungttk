<?php
use Application\Domain\EventBus\Event\EventInterface;

/**
 * Class NullEvent.
 */
class NullEvent implements EventInterface
{

    private static $instance;

    protected function __construct()
    {}

    public static function create(): NullEvent
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __clone()
    {}
}