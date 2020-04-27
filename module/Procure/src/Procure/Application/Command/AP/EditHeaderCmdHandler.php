<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApUpdateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\AccountPayable\Validator\Header\IncotermValidator;
use Procure\Domain\AccountPayable\Validator\Header\InvoiceAndPaymentTermValidator;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EditHeaderCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not foundsv!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof ApDTO) {
            throw new InvalidArgumentException("ApDTO object not found!");
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var APDoc $rootEntity ;
         * @var APSnapshot $rootSnapshot ;
         * @var ApUpdateOptions $options ;
         *     
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof ApUpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();

        try {

            $notification = new Notification();

            if ($rootEntity->getDocStatus() == ProcureDocStatus::DOC_STATUS_POSTED) {
                throw new InvalidOperationException(sprintf("AP is already posted! %s", $rootEntity->getId()));
            }

            /**
             *
             * @var APSnapshot $snapshot ;
             * @var APSnapshot $newSnapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "docNumber",
                "docDate",
                "sapDoc",
                "postingDate",
                "contractDate",
                "grDate",
                "remarks",
                "docCurrency",
                "pmtTerm",
                "warehouse",
                "incoterm",
                "remarks"
            ];

            if ($rootEntity->getDocType() == Constants::PROCURE_DOC_TYPE_INVOICE_PO) {
                $editableProperties = [
                    "isActive",
                    "docNumber",
                    "docDate",
                    "sapDoc",
                    "postingDate",
                    "grDate",
                    "warehouse",
                    "pmtTerm",
                    "remarks",
                    "docCurrency"
                ];
            }

            // $snapshot->warehouse;

            $newSnapshot = APSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);
            $changeLog = $snapshot->compare($newSnapshot);

            if ($changeLog == null) {
                // No Notify when posting.
                if (! $options->getIsPosting()) {
                    $notification->addError("Nothing change on Doc#" . $rootEntityId);
                    $dto->setNotification($notification);
                }
                return;
            }

            $params = [
                "changeLog" => $changeLog
            ];

            // do change
            $newSnapshot->lastchangeBy = $userId;

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new GrDateAndWarehouseValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new InvoiceAndPaymentTermValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new IncotermValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $cmdRepository = new APCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new APPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $newRootEntity = APDoc::updateFrom($newSnapshot, $options, $params, $headerValidators, $sharedService, $postingService);

            // event dispatcher
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($newRootEntity->getRecordedEvents());
            }

            $m = sprintf("Doc #%s updated", $newRootEntity->getId());

            $notification->addSuccess($m);

            // No Check Version when Posting when posting.
            $queryRep = new APQueryRepositoryImpl($cmd->getDoctrineEM());

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
