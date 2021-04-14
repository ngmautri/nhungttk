<?php
namespace Application\Application\EventBus\Handler\Department;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Domain\Event\Company\DepartmentInserted;
use Application\Domain\Util\Tree\Node\GenericNode;
use Inventory\Domain\Event\Item\ItemCreated;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnDeparmentInsertedSaveToLog extends AbstractEventHandler
{

    /**
     *
     * @param ItemCreated $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(DepartmentInserted $event)
    {
        try {
            if (! $event->getTarget() instanceof GenericNode) {
                Throw new \InvalidArgumentException("GenericNode not given for updating index.");
            }
            $m = \sprintf("Department [%s] Insert. This event logged!", $event->getTarget()->getNodeName());
            $this->logInfo($m);
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
        return DepartmentInserted::class;
    }
}