<?php
namespace Inventory\Application\Event\Handler\Procure;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\GoodsIssuePostedEvent;
use Inventory\Domain\Event\GoodsExchangePostedEvent;
use Procure\Domain\Event\GRNIpostedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRNIpostedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            GRNIpostedEvent::class => 'onGRNIPosted'
        ];
    }

    /**
     * 
     * @param GRNIpostedEvent $event
     */
    public function onGRNIPosted(GRNIpostedEvent $event)
    {
        echo "I am Goods Exchange handler for GRNI";
    }
}