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
class WarehouseCreatedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            WarehouseCreatedEvent::class => 'onCreated'
        ];
    }

    /**
     *
     * @param WarehouseCreatedEvent $event
     */
    public function onCreated(WarehouseCreatedEvent $event)
    {}
}