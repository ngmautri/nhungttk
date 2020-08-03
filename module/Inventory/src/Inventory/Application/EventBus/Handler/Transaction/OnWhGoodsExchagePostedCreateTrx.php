<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Domain\Event\Transaction\GI\WhGoodsExchangePosted;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhGoodsExchagePostedCreateTrx extends AbstractEventHandler
{

    public function __invoke(WhGoodsExchangePosted $event)
    {
        try {

            $this->logInfo(\sprintf("Receipt exchanged/damage goods created for WH-GI #%s", $event->getTarget()
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
        return WhGoodsExchangePosted::class;
    }
}