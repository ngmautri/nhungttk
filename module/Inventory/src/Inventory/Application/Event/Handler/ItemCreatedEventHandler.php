<?php
namespace Inventory\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCreatedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            ItemCreatedEvent::EVENT_NAME => 'onItemCreated'
        ];
    }

    /**
     *
     * @param ItemCreatedEvent $event
     */
    public function onItemCreated(ItemCreatedEvent $event)
    {
        //echo $event->getItem()->getId();
        echo "\nSymfony run";
    }
}