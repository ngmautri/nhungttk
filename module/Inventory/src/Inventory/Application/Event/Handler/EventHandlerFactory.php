<?php
namespace Inventory\Application\Event\Handler;

use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Event\GoodsExchangePostedEvent;
use Inventory\Domain\Event\WarehouseCreatedEvent;
use Inventory\Domain\Event\WarehouseUpdatedEvent;

use Inventory\Application\Event\Handler\Procure\GRIRpostedEventHandler;
use Inventory\Application\Event\Handler\Procure\GRNIpostedEventHandler;

use Procure\Domain\Event\GRIRpostedEvent;
use Procure\Domain\Event\GRNIpostedEvent;

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
            case GoodsIssuePostedEvent::class:
                $handlers[] = new GoodsIssuePostedEventHandler();
                break;

            case GoodsExchangePostedEvent::class:
                $handlers[] = new GoodsExchangePostedEventHandler();
                break;

            case WarehouseCreatedEvent::class:
                $handlers[] = new WarehouseCreatedEventHandler();
                break;

            case WarehouseUpdatedEvent::class:
                $handlers[] = new WarehouseUpdatedEventHandler();
                break;

            // procure // GRIR
            case GRIRpostedEvent::class:
                $handlers[] = new GRIRpostedEventHandler();
                break;
                
           // procure // GRNI
            case GRNIpostedEvent::class:
                $handlers[] = new GRNIpostedEventHandler();
                break;
        }

        return $handlers;
    }
}