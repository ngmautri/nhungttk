<?php
namespace ApplicationTest\EventBus;

use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;

class DummyEvent2Handler implements EventHandlerInterface, EventHandlerPriorityInterface
{

    public function __invoke($event)
    {
        echo \sprintf("\n%s involked.", __METHOD__);
        echo "\n" . \get_class($event);
    }

    public static function priority()
    {
        return 100;
    }

    public static function subscribedTo()
    {
        return DummyEvent::class;
    }
}