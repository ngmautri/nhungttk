<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Domain\Event\Transaction\GI\WhTransferLocationPosted;
use Inventory\Domain\Transaction\TrxSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhTransferLocationPostedCreateTrx extends AbstractEventHandler
{

    public function __invoke(WhTransferLocationPosted $event)
    {
        try {

            $snapshot = $event->getTarget();

            if (! $snapshot instanceof TrxSnapshot) {
                Throw new \InvalidArgumentException("TrxSnapshot not give for creating WH Trx");
            }
            /*
             * $options = new PostGRFromExchangeOptions($snapshot->getCompany(), $snapshot->getCreatedBy(), __METHOD__, $event->getTarget());
             * $cmdHanlder = new PostGrFromExchangeCmdHandler();
             * $cmd = new GenericCmd($this->getDoctrineEM(), new TrxSnapshot(), $options, $cmdHanlder);
             * $cmd->setLogger($this->getLogger());
             * $cmd->execute();
             */

            $this->logInfo(\sprintf("GR created for WH-Transfer Location #%s", $event->getTarget()
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
        return WhTransferLocationPosted::class;
    }
}