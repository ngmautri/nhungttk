<?php
namespace Inventory\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\ItemUpdatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemUpdatedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ItemUpdatedEvent::EVENT_NAME => 'onItemUpdated'
        ];
    }

    /**
     *
     * @param ItemCreatedEvent $event
     */
    public function onItemUpdated(ItemUpdatedEvent $event)
    {
        echo $event->getItem()->getId();
    }
}