<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApRowUpdateOptions;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Application\Service\FXService;
use Procure\Application\Service\AP\RowSnapshotReference;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\AccountPayable\APRowSnapshotAssembler;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\AccountPayable\Validator\Row\GLAccountValidator;
use Procure\Domain\AccountPayable\Validator\Row\WarehouseValidator;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoRowUpdateException;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

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
            throw new PoRowUpdateException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }
        /**
         *
         * @var ApRowDTO $dto ;
         * @var APDoc $rootEntity ;
         * @var ApRowUpdateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $options instanceof ApRowUpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }
        if (! $cmd->getDto() instanceof ApRowDTO) {
            throw new InvalidArgumentException("ApRowDTO object not found!");
        }

        try {
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $userId = $options->getUserId();
            $version = $options->getVersion();

            $notification = new Notification();

            /**
             *
             * @var APRow $snapshot ;
             * @var GRRowSnapshot $newSnapshot ;
             * @var GRRow $row ;
             *     
             */
            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "isActive",
                "rowNumber",
                "item",
                "prRow",
                "poRow",
                "vendorItemCode",
                "vendorItemName",
                "warehouse",
                "docQuantity",
                "docUnit",
                "docUnitPrice",
                "conversionFactor",
                "descriptionText",
                "taxRate",
                "glAccount",
                "costCenter",
                "remarks"
            ];

            $newSnapshot = APRowSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);

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

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new WarehouseValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new GLAccountValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new APCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new APPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $newSnapshot = RowSnapshotReference::updateReferrence($newSnapshot, $cmd->getDoctrineEM()); // update referrence before update.
            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("PO #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());

            $notification->addSuccess($m);

            $queryRep = new APQueryRepositoryImpl($cmd->getDoctrineEM());
            // revision numner hasnt been increased.
            $currentVersion = $queryRep->getVersion($rootEntity->getId()) - 1;
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! ", $version, $currentVersion));
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
