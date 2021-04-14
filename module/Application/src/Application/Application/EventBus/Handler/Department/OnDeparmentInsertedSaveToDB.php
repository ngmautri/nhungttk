<?php
namespace Application\Application\EventBus\Handler\Department;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Domain\Event\Company\DepartmentInserted;
use Inventory\Domain\Event\Item\ItemCreated;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnDeparmentInsertedSaveToDB extends AbstractEventHandler
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
            if (! $event->getTarget() instanceof ItemSnapshot) {
                Throw new \InvalidArgumentException("ItemSnapshot not given for updating index.");
            }
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