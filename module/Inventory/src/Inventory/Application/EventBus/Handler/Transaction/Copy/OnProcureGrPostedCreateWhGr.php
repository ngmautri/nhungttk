<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\Transaction\PostCopyFromProcureGRCmdHandler;
use Inventory\Application\Command\Transaction\Options\PostCopyFromProcureGROptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Procure\Application\Command\GenericCmd;
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

            $target = $event->getTarget();

            if (! $target instanceof GRSnapshot) {
                Throw new \InvalidArgumentException("GRSnapshot not give for creating WH Trx");
            }

            $rootSnapshot = $event->getTarget();
            $id = $rootSnapshot->getId();
            $token = $rootSnapshot->getToken();

            $rep = new GRQueryRepositoryImpl($this->getDoctrineEM());
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            $options = new PostCopyFromProcureGROptions($rootSnapshot->getCompany(), $rootEntity->getCreatedBy(), __METHOD__, $event->getTarget());
            $dto = new TrxDTO();

            $cmdHandler = new PostCopyFromProcureGRCmdHandler(); // No transactional
            $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandler, $this->getEventBusService());
            $cmd->execute();
            $this->logInfo(\sprintf("WH-GR created from PO-GR!  #%s ", $target->getId()));
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