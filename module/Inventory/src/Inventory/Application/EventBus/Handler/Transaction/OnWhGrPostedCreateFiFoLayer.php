<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhGrPostedCreateFiFoLayer extends AbstractEventHandler
{

    /**
     *
     * @param WhGrPosted $event
     */
    public function __invoke(WhGrPosted $event)
    {
        try {
            $trx = $event->getTarget();
            if (! $trx instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service! OnWhGrPostedCreateFiFoLayer");
            }

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($this->getDoctrineEM());
            $fifoService->createLayersFor($event->getTarget());

            $this->logInfo(\sprintf("FIFO Layer for WH-GR #%s-%s created!", $trx->getId(), $trx->getSysNumber()));
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());

            throw $e;
        }
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return WhGrPosted::class;
    }
}