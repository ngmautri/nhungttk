<?php
namespace Procure\Application\Event\Handler;

use Procure\Domain\Event\POCreatedEvent;
use Procure\Domain\Event\PoHeaderCreatedEvent;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventHandlerFactory
{
    public static function createEventHandler($eventName, EntityManager $doctrineEM = null)
    {
        if (! $doctrineEM instanceof EntityManager) {
            return null;
        }

        $handlers = array();
        switch ($eventName) {
            case POCreatedEvent::class:
                $handlers[] = new PoHeaderCreatedEventHandler();
                break;
            case PoHeaderCreatedEvent::class:
                $handlers[] = new PoHeaderCreatedEventHandler($doctrineEM);
                break;
        }
        
        return $handlers;
    }
}