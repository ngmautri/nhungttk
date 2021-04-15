<?php
namespace Application\Application\EventBus\Handler\Department;

use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\SaveDepartmentCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Application\Domain\Event\Company\DepartmentInserted;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnDepartmentInsertedSaveToDB extends AbstractEventHandler
{

    /**
     *
     * @param DepartmentInserted $event
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __invoke(DepartmentInserted $event)
    {
        try {
            if (! $event->getTarget() instanceof GenericNode) {
                Throw new \InvalidArgumentException("GenericNode");
            }

            $options = $event->getParam("options");

            if (! $options instanceof CmdOptions) {
                Throw new \InvalidArgumentException("CmdOptions empty!");
            }

            $cmdHandler = new SaveDepartmentCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $event->getTarget(), $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();

            $m = \sprintf("Department [%s] save to DB", $event->getTarget()->getNodeName());
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
        return DepartmentInserted::class;
    }
}