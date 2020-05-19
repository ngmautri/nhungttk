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
        if (! $event->getTarget() instanceof GRSnapshot) {
            Throw new \InvalidArgumentException("GRSnapshot not give for Serial No");
        }

        $sv = new SerialNoServiceImpl();
        $sv->setDoctrineEM($this->getDoctrineEM());
        $sv->createSerialNoFor($event->getTarget());

        $this->getLogger()->info(\sprintf("Serial No for WH-GR #%s created!", $event->getTarget()
            ->getId()));
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