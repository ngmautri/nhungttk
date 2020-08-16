<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Transaction\PostGrFromExchangeCmdHandler;
use Inventory\Application\Command\Transaction\Options\PostGRFromExchangeOptions;
use Inventory\Domain\Event\Transaction\GI\WhGoodsExchangePosted;
use Inventory\Domain\Transaction\TrxSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhTransferPostedCreateTrx extends AbstractEventHandler
{

    public function __invoke(WhGoodsExchangePosted $event)
    {
        try {

            $snapshot = $event->getTarget();

            if (! $snapshot instanceof TrxSnapshot) {
                Throw new \InvalidArgumentException("TrxSnapshot not give for creating WH Trx");
            }

            $options = new PostGRFromExchangeOptions($snapshot->getCompany(), $snapshot->getCreatedBy(), __METHOD__, $event->getTarget());
            $cmdHanlder = new PostGrFromExchangeCmdHandler();
            $cmd = new GenericCmd($this->getDoctrineEM(), new TrxSnapshot(), $options, $cmdHanlder);
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
            $this->logInfo(\sprintf("GR created for WH-Transfer Warehouse #%s", $event->getTarget()
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
        return WhGoodsExchangePosted::class;
    }
}