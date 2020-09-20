<?php
namespace Procure\Application\Command\QR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\QR\Options\UpdateOptions;
use Procure\Application\DTO\Qr\QrDTO;
use Procure\Domain\Exception\DBUpdateConcurrencyException;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\QuotationRequest\QRSnapshot;
use Procure\Domain\QuotationRequest\QRSnapshotAssembler;
use Procure\Domain\QuotationRequest\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

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
            throw new InvalidArgumentException(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if (! $cmd->getDto() instanceof QrDTO) {
            throw new InvalidArgumentException("QrDTO object not found!");
        }

        /**
         *
         * @var QrDTO $dto ;
         * @var QRDoc $rootEntity ;
         * @var QRSnapshot $rootSnapshot ;
         * @var UpdateOptions $options ;
         *
         */
        $options = $cmd->getOptions();
        $dto = $cmd->getDto();

        if (! $options instanceof UpdateOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $options = $cmd->getOptions();
        $rootEntity = $options->getRootEntity();
        $rootEntityId = $options->getRootEntityId();
        $version = $options->getVersion();
        $userId = $options->getUserId();

        try {

            $notification = new Notification();

            if ($rootEntity->getDocStatus() == ProcureDocStatus::POSTED) {
                throw new InvalidOperationException(sprintf("QR is already posted! %s", $rootEntity->getId()));
            }

            /**
             *
             * @var QRSnapshot $snapshot ;
             * @var QRSnapshot $newSnapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $editableProperties = [
                "docNumber",
                "docDate",
                "vendor",
                "docCurrency",
                "remarks",
                "incoterm",
                "incotermPlace",
                "pmtTerm",
                "warehouse"
            ];

            $newSnapshot = QRSnapshotAssembler::updateSnapshotFieldsFromDTO($newSnapshot, $dto, $editableProperties);
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

            // var_dump($changeLog);

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $cmdRepository = new QRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new QrPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $newRootEntity = QRDoc::updateFrom($newSnapshot, $options, $params, $headerValidators, $sharedService, $postingService);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            // ================
            $m = sprintf("GR #%s updated", $newRootEntity->getId());

            $notification->addSuccess($m);

            // No Check Version when Posting when posting.
            $queryRep = new QRQueryRepositoryImpl($cmd->getDoctrineEM());

            // time to check version - concurency
            $currentVersion = $queryRep->getVersion($rootEntityId) - 1;

            // revision numner has been increased.
            if ($version != $currentVersion) {
                throw new DBUpdateConcurrencyException(sprintf("Object version has been changed from %s to %s since retrieving. Please retry! %s", $version, $currentVersion, ""));
            }
            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
