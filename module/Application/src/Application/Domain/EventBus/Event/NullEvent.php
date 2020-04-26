<?php
namespace Application\Domain\EventBus\Event;

/**
 * Class NullEvent.
 */
class NullEvent implements EventInterface
{

    private static $instance;

    protected function __construct()
    {}

    public static function create()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __clone()
    {}
}