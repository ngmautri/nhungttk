<?php
namespace Procure\Application\EventBus\Handler\AP;

use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Domain\Event\Ap\ApPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateIndexOnApPosted implements EventHandlerInterface, EventHandlerPriorityInterface
{

    /**
     *
     * @param ApPosted $event
     */
    public function __invoke(ApPosted $event)
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
        return ApPosted::class;
    }
}