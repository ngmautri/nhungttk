<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\PostCopyFromCmdOptions;
use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\Transaction\Doctrine\PostCopyFromProcureGRCmdHandler;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnProcureGrPostedCreateWhGr extends AbstractEventHandler
{

    /**
     *
     * @param GrPosted $event
     * @throws \InvalidArgumentException
     */
    public function __invoke(GrPosted $event)
    {
        try {

            $rootSnapshot = $event->getTarget();

            if (! $rootSnapshot instanceof GRSnapshot) {
                Throw new \InvalidArgumentException("GRSnapshot not give for creating WH Trx");
            }

            $rep = new GRQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getRootEntityByTokenId($rootSnapshot->getId(), $rootSnapshot->getToken());

            $companyVO = $this->getCompanyVO($rootEntity->getCompany());
            $options = new PostCopyFromCmdOptions($companyVO, $rootEntity->getCreatedBy(), __METHOD__, $rootEntity);

            $cmdHandler = new PostCopyFromProcureGRCmdHandler(); // No transactional

            $cmd = new GenericCommand($this->getDoctrineEM(), null, $options, $cmdHandler, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());

            $cmd->execute();
            $this->logInfo(\sprintf("WH-GR created from PO-GR!  #%s ", $rootSnapshot->getId()));
        } catch (\Exception $e) {

            // There might be nothing to receive in stock, so do not throw exception, just log it.
            $this->logException($e);
        }
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