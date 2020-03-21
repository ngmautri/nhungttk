<?php
namespace Procure\Application\Event\Handler;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;
use Procure\Application\Event\Handler\PO\PoHeaderCreatedHandler;
use Procure\Application\Event\Handler\PO\PoHeaderUpdatedHandler;
use Procure\Application\Event\Handler\PO\PoRowAddedHandler;
use Procure\Application\Event\Handler\PO\PoRowUpdatedHandler;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Application\Event\Handler\PO\PoPostedHandler;

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

            case PoRowAdded::class:
                $handlers[] = new PoRowAddedHandler($doctrineEM);
                break;
                
            case PoRowUpdated::class:
                $handlers[] = new PoRowUpdatedHandler($doctrineEM);
                break;
                
            case PoPosted::class:
                $handlers[] = new PoPostedHandler($doctrineEM);
                break;
        }

        return $handlers;
    }
}