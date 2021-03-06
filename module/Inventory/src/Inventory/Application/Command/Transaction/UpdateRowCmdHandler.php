<?php
namespace Inventory\Application\Command\Transaction;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\TrxRowUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxRowDTO;
use Inventory\Application\Service\SharedServiceFactory;
use Inventory\Application\Service\Transaction\RowSnapshotReference;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\TrxRowSnapshotAssembler;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }
        /**
         *
         * @var TrxRowDTO $dto ;
         * @var TrxDoc $rootEntity ;
         * @var TrxRowUpdateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $options instanceof TrxRowUpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }
        if (! $cmd->getDto() instanceof TrxRowDTO) {
            throw new InvalidArgumentException("TrxRowDTO not found!");
        }

        try {
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();
            $version = $options->getVersion();
            $notification = new Notification();

            /**
             *
             * @var TrxRowSnapshot $snapshot ;
             * @var TrxRowSnapshot $newSnapshot ;
             * @var TrxRow $row ;
             *
             */
            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "isActive",
                "docQuantity",
                "conversionFactor",
                "docUnitPrice",
                "remarks"
            ];

            $newSnapshot = TrxRowSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);

            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                $notification->addError("Nothing change on row#" . $row->getId());
                $dto->setNotification($notification);
                return;
            }
            // var_dump($changeLog);
            $params = [
                "rowId" => $row->getId(),
                "rowToken" => $row->getToken(),
                "changeLog" => $changeLog
            ];

            // do change

            $newSnapshot = RowSnapshotReference::updateReferrence($newSnapshot, $cmd->getDoctrineEM()); // update referrence before update.

            $sharedService = SharedServiceFactory::createForTrx($cmd->getDoctrineEM());
            $sharedService->setLogger($cmd->getLogger());

            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("Trx Row #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());

            $notification->addSuccess($m);

            $queryRep = new TrxQueryRepositoryImpl($cmd->getDoctrineEM());
            // revision numner hasnt been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
