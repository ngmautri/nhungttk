<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\CopyFromGROptions;
use Inventory\Application\Command\Transaction\Options\PostCopyFromProcureGROptions;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Exception\OperationFailedException;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GenericGR;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromProcureGRReversalCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        /**
         *
         * @var \Inventory\Application\DTO\Transaction\TrxDTO $dto ;
         * @var GenericGR $rootEntity ;
         * @var CopyFromGROptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof PostCopyFromProcureGROptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof GRSnapshot) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "PO-GR Document required"));
        }

        try {

            $notification = new Notification();

            $id = $sourceObj->getId();
            $token = $sourceObj->getToken();

            $rep = new GRQueryRepositoryImpl($cmd->getDoctrineEM());
            $sourceObj = $rep->getRootEntityByTokenId($id, $token);

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());
            $sharedService->setLogger($cmd->getLogger());

            $rootEntity = TransactionFactory::postCopyFromProcureGRReversal($sourceObj, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GR #%s copied from PO-GR #%s and posted!", $rootEntity->getId(), $sourceObj->getId());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
