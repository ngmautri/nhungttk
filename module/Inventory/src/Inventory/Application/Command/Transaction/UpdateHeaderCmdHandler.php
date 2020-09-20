<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\TrxUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\TrxSnapshotAssembler;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Shared\ProcureDocStatus;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateHeaderCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \InvalidArgumentException(sprintf("% not foundsv!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof TrxDTO) {
            throw new InvalidArgumentException("TrxDTO object not found!");
        }

        /**
         *
         * @var TrxDTO $dto ;
         * @var TrxDoc $rootEntity ;
         * @var TrxSnapshot $rootSnapshot ;
         * @var TrxUpdateOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof TrxUpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();

        try {

            $notification = new Notification();

            if ($rootEntity->getDocStatus() == ProcureDocStatus::POSTED) {
                throw new \RuntimeException(sprintf("Trx is already posted! %s", $rootEntity->getId()));
            }

            /**
             *
             * @var TrxSnapshot $snapshot ;
             * @var TrxSnapshot $newSnapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "movementDate",
                "isActive",
                "warehouse",
                "targetWarehouse",
                "remarks"
            ];

            // $snapshot->warehouse;

            $newSnapshot = TrxSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);
            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                // No Notify when posting.
                if (! $options->getIsPosting()) {
                    $notification->addError("Nothing change on Doc#" . $rootEntityId);
                    $dto->setNotification($notification);
                }
                return;
            }

            // \var_dump($changeLog);

            $params = [
                "changeLog" => $changeLog
            ];

            // do change

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $cmdRepository = new TrxCmdRepositoryImpl($cmd->getDoctrineEM());

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($cmd->getDoctrineEM());
            $valuationService = new TrxValuationService($fifoService);

            $postingService = new TrxPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService, $postingService);
            $sharedService->setValuationService($valuationService);

            $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($cmd->getDoctrineEM()));

            $newRootEntity = TransactionFactory::updateFrom($newSnapshot, $options, $sharedService, $params);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $m = sprintf("Trx #%s updated", $newRootEntity->getId());

            $notification->addSuccess($m);

            // No Check Version when Posting when posting.
            $queryRep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! %s", $version, $currentVersion, ""));
            }
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
