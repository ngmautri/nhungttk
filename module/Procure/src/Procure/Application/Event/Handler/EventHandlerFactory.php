<?php
namespace Procure\Application\Event\Handler;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;

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
            case PoHeaderCreated::class:
                $handlers[] = new PoHeaderCreatedHandler($doctrineEM);
                break;

            case PoHeaderUpdated::class:
                $handlers[] = new PoHeaderUpdatedHandler($doctrineEM);
                break;
        }

        return $handlers;
    }
}