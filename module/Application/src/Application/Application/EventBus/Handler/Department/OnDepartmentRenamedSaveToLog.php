<?php
namespace Application\Application\EventBus\Handler\Department;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Domain\Event\Company\DepartmentRenamed;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnDepartmentRenamedSaveToLog extends AbstractEventHandler
{

    /**
     *
     * @param DepartmentRenamed $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(DepartmentRenamed $event)
    {
        try {
            if (! $event->getTarget() instanceof GenericNode) {
                Throw new \InvalidArgumentException("GenericNode");
            }

            $oldName = $event->getParam("oldName");

            $m = \sprintf("Department [%s] renamed to %s and logged!", $oldName, $event->getTarget()->getNodeName());
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
        return DepartmentRenamed::class;
    }
}