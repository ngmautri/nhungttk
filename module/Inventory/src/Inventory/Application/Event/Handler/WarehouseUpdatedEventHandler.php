<?php
namespace Inventory\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\WarehouseCreatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseUpdatedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            WarehouseUpdatedEventHandler::class => 'onUpdated'
        ];
    }

    /**
     *
     * @param WarehouseUpdatedEventHandler $event
     */
    public function onUpdated(WarehouseUpdatedEventHandler $event)
    {}
}