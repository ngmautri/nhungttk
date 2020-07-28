<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnOpenBalancePostedCloseTrx extends AbstractEventHandler
{

    /**
     *
     * @param WhOpenBalancePosted $event
     * @throws \InvalidArgumentException
     */
    public function __invoke(WhOpenBalancePosted $event)
    {
        try {

            // close all transations.

            $this->getLogger()->info(\sprintf("Transactions closed on opening balance posted!  #%s ", $event->getTarget()
                ->getId()));
        } catch (\Exception $e) {

            // There might be nothing to receive in stock, so do not throw exception, just log it.
            $this->logException($e);
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return WhOpenBalancePosted::class;
    }
}