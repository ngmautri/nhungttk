<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\PostCopyFromCmdOptions;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\Transaction\Doctrine\PostGrFromTransferWhCmdHandler;
use Inventory\Domain\Event\Transaction\GI\WhTransferPosted;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnWhTransferPostedCreateTrx extends AbstractEventHandler
{

    public function __invoke(WhTransferPosted $event)
    {
        try {

            $snapshot = $event->getTarget();

            if (! $snapshot instanceof TrxSnapshot) {
                Throw new \InvalidArgumentException("TrxSnapshot not give for creating WH Trx");
            }

            $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getRootEntityByTokenId($snapshot->getId(), $snapshot->getToken());

            $companyVO = $this->getCompanyVO($rootEntity->getCompany());
            $options = new PostCopyFromCmdOptions($companyVO, $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

            $cmdHandler = new PostGrFromTransferWhCmdHandler(); // No transactional

            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHandler, $this->getEventBusService());
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
        return WhTransferPosted::class;
    }
}