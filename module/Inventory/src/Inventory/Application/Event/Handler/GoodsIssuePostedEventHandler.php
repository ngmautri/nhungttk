<?php
namespace Inventory\Application\Event\Handler;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\GoodsIssuePostedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GoodsIssuePostedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            GoodsIssuePostedEvent::class => 'onGoodsIssuePosted'
        ];
    }

    /**
     *
     * @param ItemCreatedEvent $event
     */
    public function onGoodsIssuePosted(GoodsIssuePostedEvent $event)
    {
        echo "I am Goods Issue handler";
    }
}