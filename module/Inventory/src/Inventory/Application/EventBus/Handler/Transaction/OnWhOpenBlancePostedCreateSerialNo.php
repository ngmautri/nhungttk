<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Service\Item\SerialNoServiceImpl;
use Inventory\Domain\Event\Transaction\GR\WhGrPosted;
use Inventory\Domain\Event\Transaction\GR\WhOpenBalancePosted;
use Procure\Domain\GoodsReceipt\GRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhOpenBlancePostedCreateSerialNo extends AbstractEventHandler
{

    /**
     *
     * @param WhGrPosted $event
     */
    public function __invoke(WhOpenBalancePosted $event)
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
        return WhOpenBalancePosted::class;
    }
}