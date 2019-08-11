<?php
namespace Inventory\Application\Event\Handler\Procure;

use Procure\Domain\Event\GRIRpostedEvent;
use Procure\Domain\Event\GRNIpostedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRIRpostedEventHandler implements EventSubscriberInterface
{

    /**
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            GRNIpostedEvent::class => 'onGRIRPosted'
        ];
    }

   /**
    * 
    * @param GRIRpostedEvent $event
    */
    public function onGRIRPosted(GRIRpostedEvent $event)
    {
        echo "I am Goods Exchange handler for GRNI";
    }
}