<?php
namespace Procure\Application\EventBus\Handler\PO;

use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Domain\Event\Po\PoPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateIndexOnPoPosted implements EventHandlerInterface, EventHandlerPriorityInterface
{

    /**
     *
     * @param PoPosted $event
     */
    public function __invoke(PoPosted $event)
    {
        echo "\n RUNNING";
        echo \sprintf("\n%s involked.", __METHOD__);
        echo "\n" . \get_class($event);
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return PoPosted::class;
    }
}