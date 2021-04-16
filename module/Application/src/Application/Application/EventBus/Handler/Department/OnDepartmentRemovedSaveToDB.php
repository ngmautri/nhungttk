<?php
namespace Application\Application\EventBus\Handler\Department;

use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\SaveDepartmentCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Application\Service\Department\Tree\DepartmentNode;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Domain\Event\Company\DepartmentMoved;
use Application\Domain\Event\Company\DepartmentRemoved;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnDepartmentRemovedSaveToDB extends AbstractEventHandler
{

    /**
     *
     * @param DepartmentMoved $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(DepartmentRemoved $event)
    {
        try {
            if (! $event->getTarget() instanceof GenericNode) {
                Throw new \InvalidArgumentException("GenericNode");
            }

            $options = $event->getParam("options");

            if (! $options instanceof CmdOptions) {
                Throw new \InvalidArgumentException("CmdOptions empty!");
            }

            /**
             *
             * @var DepartmentNode $n
             */
            $t = $event->getTarget();

            // update context object:

            /**
             *
             * @var DepartmentSnapshot $contextObj ;
             */
            $contextObj = $t->getContextObject();
            $contextObj->setNodeParentId(null);
            $contextObj->setRemarks("Removed from parent");
            $contextObj->setParentName(null);

            $createdDate = new \Datetime();
            $contextObj->setLastChangeOn(date_format($createdDate, 'Y-m-d H:i:s'));

            $cmdHandler = new SaveDepartmentCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $t, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();

            $m = \sprintf("Department [%s] moved and saved to DB", $event->getTarget()->getNodeName());
            // call SaveToDatabaseCmd
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
        return DepartmentRemoved::class;
    }
}