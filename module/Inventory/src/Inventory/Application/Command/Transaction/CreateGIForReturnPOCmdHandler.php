<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\CreateTrxFromGRFromPurchasingOptions;
use Inventory\Application\Command\Transaction\Options\TrxCreateOptions;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\GI\GIforReturnPO;
use Inventory\Domain\Transaction\GR\GRFromPurchasing;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateGIForReturnPOCmdHandler extends AbstractCommandHandler
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
         * @var TrxDoc $rootEntity ;
         * @var TrxCreateOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof CreateTrxFromGRFromPurchasingOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        /**
         *
         * @var TrxSnapshot $sourceObj ;
         */
        $sourceObj = $options->getSourceObj();

        if (! $sourceObj instanceof GRFromPurchasing) {
            throw new InvalidArgumentException(sprintf("Source object not given! %s", "WH-GR from purchasing required"));
        }

        try {

            $notification = new Notification();

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM(), $cmd->getLogger());

            $id = $sourceObj->getId();
            $token = $sourceObj->getToken();

            $rep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());
            $sourceObject = $rep->getRootEntityByTokenId($id, $token);

            $rootEntity = GIforReturnPO::createFromGRFromPurchasing($sourceObject, $options, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("WH-GI for return PO #%s created from %s!", $rootEntity->getId(), $sourceObj->getSysNumber());
            $notification->addSuccess($m);
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
