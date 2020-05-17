<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Domain\Event\Transaction\TrxPosted;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateFiFoLayerOnTrxPosted extends AbstractEventHandler
{

    /**
     *
     * @param TrxPosted $event
     */
    public function __invoke(TrxPosted $event)
    {
        if (! $event->getTarget() instanceof GenericTrx) {
            Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service!");
        }

        $fifoService = new FIFOServiceImpl();
        $fifoService->setDoctrineEM($this->getDoctrineEM());
        $fifoService->createLayersFor($event->getTarget());

        $this->getLogger()->info(\sprintf("FIFO Layer for Trx #%s created!", $event->getTarget()
            ->getId()));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return TrxPosted::class;
    }
}