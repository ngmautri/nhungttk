<?php
namespace Procure\Application\Command\QR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\QR\Options\RowUpdateOptions;
use Procure\Application\DTO\Qr\QrRowDTO;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\QuotationRequest\QRRowSnapshot;
use Procure\Domain\QuotationRequest\QRRowSnapshotAssembler;
use Procure\Domain\QuotationRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\QuotationRequest\Validator\Row\DefaultRowValidator;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

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
            throw new InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }
        /**
         *
         * @var QrRowDTO $dto ;
         * @var QRDoc $rootEntity ;
         * @var RowUpdateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $options instanceof RowUpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }
        if (! $cmd->getDto() instanceof QrRowDTO) {
            throw new InvalidArgumentException("QrRowDTO object not found!");
        }

        try {
            $rootEntity = $options->getRootEntity();
            $localEntity = $options->getLocalEntity();

            $userId = $options->getUserId();
            $version = $options->getVersion();

            $notification = new Notification();

            /**
             *
             * @var QRRowSnapshot $snapshot ;
             * @var QRRowSnapshot $newSnapshot ;
             * @var QRRow $row ;
             *     
             */
            $row = $localEntity;
            $snapshot = $row->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "isActive",
                "rowNumber",
                "prRow",
                "item",
                "vendorItemCode",
                "vendorItemName",
                "warehouse",
                "docQuantity",
                "docUnit",
                "taxRate",
                "docUnitPrice",
                "conversionFactor",
                "discountRate",
                "glAccount",
                "costCenter",
                "remarks"
            ];

            $newSnapshot = QRRowSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);

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
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new QRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new QrPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $rootEntity->updateRowFrom($newSnapshot, $options, $params, $headerValidators, $rowValidators, $sharedService, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================

            $m = sprintf("PR #%s updated. Memory used #%s", $rootEntity->getId(), memory_get_usage());

            $notification->addSuccess($m);

            $queryRep = new QRQueryRepositoryImpl($cmd->getDoctrineEM());
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
