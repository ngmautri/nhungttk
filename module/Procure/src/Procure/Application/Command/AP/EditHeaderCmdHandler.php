<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\AP\Options\ApUpdateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
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
                "incotermPlace",
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
                    "docCurrency",
                    "remarks"
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

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $postingService = new APPostingService(new APCmdRepositoryImpl($cmd->getDoctrineEM()));
            $fxService = new FXServiceImpl();

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $domainSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

            $validationService = ValidatorFactory::createForHeader($sharedService);

            $newRootEntity = APDoc::updateFrom($newSnapshot, $options, $params, $validationService, $sharedService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
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
