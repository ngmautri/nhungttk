<?php
namespace Procure\Application\Event\Handler;

use Procure\Domain\Event\POCreatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventHandlerFactory
{

    public static function createEventHandler($eventName)
    {
        $handlers = array();
        switch ($eventName) {
            case POCreatedEvent::class:
                $handlers[] = new POCreatedEventHandler();
                break;
        }

        return $handlers;
    }
}