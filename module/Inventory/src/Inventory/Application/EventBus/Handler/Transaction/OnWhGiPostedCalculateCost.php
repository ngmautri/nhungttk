<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Domain\Event\Transaction\GI\WhGiPosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhGiPostedCalculateCost extends AbstractEventHandler
{

    /**
     *
     * @param WhGiPosted $event
     */
    public function __invoke(WhGiPosted $event)
    {
        try {

            $this->getLogger()->info(\sprintf("COGS for WH-GI #%s caculated!", $event->getTarget()
                ->getId()));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return WhGiPosted::class;
    }
}