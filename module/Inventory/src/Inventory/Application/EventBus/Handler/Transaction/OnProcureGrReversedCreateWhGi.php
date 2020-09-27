<?php
namespace Inventory\Application\EventBus\Handler\Transaction;

use Application\Application\EventBus\Contracts\AbstractEventHandler;
use Application\Domain\EventBus\Handler\EventHandlerPriorityInterface;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Transaction\PostCopyFromProcureGRReversalCmdHandler;
use Inventory\Application\Command\Transaction\Options\PostCopyFromProcureGROptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnProcureGrReversedCreateWhGi extends AbstractEventHandler
{

    /**
     *
     * @param GrReversed $event
     * @throws \InvalidArgumentException
     */
    public function __invoke(GrReversed $event)
    {
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

        $cmdHandler = new PostCopyFromProcureGRReversalCmdHandler(); // No transactional
        $cmd = new GenericCmd($this->getDoctrineEM(), $dto, $options, $cmdHandler, $this->getEventBusService());
        $cmd->execute();
        $this->logInfo(\sprintf("WH-GI created from PO-GR Reversal!  #%s ", $target->getId()));
    }

    public static function priority()
    {
        return EventHandlerPriorityInterface::HIGH_PRIORITY;
    }

    public static function subscribedTo()
    {
        return GrReversed::class;
    }
}