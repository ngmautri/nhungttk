<?php
namespace Inventory\Application\Event\Handler;

use Inventory\Domain\Event\GoodsExchangePostedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            GoodsExchangePostedEvent::class => 'onGoodsExchangePosted'
        ];
    }

    /**
     *
     * @param GoodsExchangePostedEvent $event
     */
    public function onGoodsExchangePosted(GoodsExchangePostedEvent $event)
    {
        // throw new \Exception( "I am Goods Exchange handler");
    }
}