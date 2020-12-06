<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\PostCopyFromCmdOptions;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\Transaction\Doctrine\PostGrFromExchangeCmdHandler;
use Inventory\Domain\Event\Transaction\GI\WhGoodsExchangePosted;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnWhGoodsExchagePostedCreateTrx extends AbstractEventHandler
{

    public function __invoke(WhGoodsExchangePosted $event)
    {
        try {

            $rootSnapshot = $event->getTarget();

            if (! $rootSnapshot instanceof TrxSnapshot) {
                Throw new \InvalidArgumentException("TrxSnapshot not give for creating WH Trx");
            }

            $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getRootEntityByTokenId($rootSnapshot->getId(), $rootSnapshot->getToken());

            $companyVO = $this->getCompanyVO($rootEntity->getCompany());
            $options = new PostCopyFromCmdOptions($companyVO, $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

            $cmdHandler = new PostGrFromExchangeCmdHandler(); // No transactional

            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHandler, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $this->logInfo(\sprintf("Receipt exchanged/damage goods created for WH-GI #%s", $rootSnapshot->getId()));
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