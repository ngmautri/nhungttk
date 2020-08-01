<?php
namespace Inventory\Application\Command\;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Inventory\Application\Command\Transaction\Options\TrxUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Transaction\TrxDoc;
use Inventory\Domain\Transaction\TrxSnapshot;
use Inventory\Domain\Transaction\TrxSnapshotAssembler;
use Inventory\Domain\Transaction\Factory\TransactionFactory;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;

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
            throw new InvalidArgumentException(sprintf("% not foundsv!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof TrxDoc) {
            throw new InvalidArgumentException("ApDTO object not found!");
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
        $userId = $options->getUserId();

        try {

            $notification = new Notification();

            if ($rootEntity->getDocStatus() == ProcureDocStatus::DOC_STATUS_POSTED) {
                throw new InvalidOperationException(sprintf("Trx is already posted! %s", $rootEntity->getId()));
            }

            /**
             *
             * @var TrxSnapshot $snapshot ;
             * @var TrxSnapshot $newSnapshot ;
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

            $params = [
                "changeLog" => $changeLog
            ];

            // do change
            $newSnapshot->lastchangeBy = $userId;

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $cmdRepository = new TrxCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new TrxPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $newRootEntity = TransactionFactory::createFrom($snapshot, $options, $sharedService);

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
