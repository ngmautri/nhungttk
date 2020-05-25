<?php
namespace Inventory\Application\EventBus\Handler\Item;

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
class CreateFiFoLayerOnWhGrPosted extends AbstractEventHandler
{

    /**
     *
     * @param WhGrPosted $event
     */
    public function __invoke(WhGrPosted $event)
    {
        try {
            if (! $event->getTarget() instanceof GenericTrx) {
                Throw new \InvalidArgumentException("GenericTrx not give for FIFO Layer Service!");
            }

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($this->getDoctrineEM());
            $fifoService->closeLayersOf($event->getTarget());

            $this->getLogger()->info(\sprintf("FIFO Layer for WH-GR #%s created!", $event->getTarget()
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
        return WhGrPosted::class;
    }
}