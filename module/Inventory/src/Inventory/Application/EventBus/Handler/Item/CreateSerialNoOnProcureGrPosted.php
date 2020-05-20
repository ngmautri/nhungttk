<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Item\SerialNoServiceImpl;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\GoodsReceipt\GRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateSerialNoOnProcureGrPosted extends AbstractEventHandler
{

    /**
     *
     * @param GrPosted $event
     */
    public function __invoke(GrPosted $event)
    {
        try {

            if (! $event->getTarget() instanceof GRSnapshot) {
                Throw new \InvalidArgumentException("GRSnapshot not give for Serial No");
            }

            $sv = new SerialNoServiceImpl();
            $sv->setDoctrineEM($this->getDoctrineEM());
            $sv->createSerialNoFor($event->getTarget());

            $this->getLogger()->info(\sprintf("Serial No for PO-GR#%s handled and created, if any!", $event->getTarget()
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
        return GrPosted::class;
    }
}