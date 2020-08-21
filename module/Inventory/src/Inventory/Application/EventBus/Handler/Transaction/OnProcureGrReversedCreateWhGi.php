<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\GoodsReceipt\GRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnProcureGrReversedCreateWhGi extends AbstractEventHandler
{

    /**
     *
     * @param GrReversed $event
     * @throws \InvalidArgumentException
     */
    public function __invoke(GrReversed $event)
    {
        if (! $event->getTarget() instanceof GRSnapshot) {
            Throw new \InvalidArgumentException("GRSnapshot not given for creating WH Trx");
        }

        $rootSnapshot = $event->getTarget();
        $id = $rootSnapshot->getId();
        $token = $rootSnapshot->getToken();

        $this->logInfo(\sprintf("WH-GR Reversed from PO-GR Reversed!  #%s ", $rootSnapshot->getId()));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return GrReversed::class;
    }
}