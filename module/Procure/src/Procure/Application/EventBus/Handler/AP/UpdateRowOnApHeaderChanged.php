<?php
namespace Procure\Application\EventBus\Handler\AP;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Domain\Event\Ap\ApPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowOnApHeaderChanged extends AbstractEventHandler
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

        $this->getLogger()->info(\sprintf("Search index created on %s, #%s ", \get_class($event), $event->getTarget()
            ->getId()));
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