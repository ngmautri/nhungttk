<?php
namespace Procure\Application\EventBus\Handler\PR;

use Application\Domain\EventBus\Handler\EventHandlerInterface;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Domain\Event\Pr\PrPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UpdateIndexOnPrSubmitted implements EventHandlerInterface, EventHandlerPriorityInterface
{

    /**
     *
     * @param PrPosted $event
     */
    public function __invoke(PrPosted $event)
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
        return PrPosted::class;
    }
}