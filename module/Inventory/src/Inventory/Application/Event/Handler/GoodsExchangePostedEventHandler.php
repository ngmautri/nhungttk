<?php
namespace Inventory\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Event\GoodsExchangePostedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GoodsExchangePostedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            GoodsExchangePostedEvent::class => 'onGoodseExchangePosted'
        ];
    }

    /**
     *
     * @param ItemCreatedEvent $event
     */
    public function onGoodseExchangePosted(GoodsExchangePostedEvent $event)
    {
        echo "I am Goods Exchange handler";
    }
}