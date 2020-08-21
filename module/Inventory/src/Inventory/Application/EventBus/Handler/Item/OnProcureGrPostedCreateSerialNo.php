<?php
namespace Inventory\Application\EventBus\Handler\Item;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Item\SerialNoServiceImpl;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\GoodsReceipt\GRSnapshot;

/**
 * This should be also for none-inventory item
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnProcureGrPostedCreateSerialNo extends AbstractEventHandler
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
            $sv->setLogger($this->getLogger());
            $sv->setDoctrineEM($this->getDoctrineEM());
            $sv->createSerialNoFor($event->getTarget());

            $this->logInfo(\sprintf("Serial No for PO-GR#%s handled and created, if any!", $event->getTarget()
                ->getId()));
        } catch (\Exception $e) {
            $this->logException($e);
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