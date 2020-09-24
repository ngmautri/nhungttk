<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\AP\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APFromPO;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SaveCopyFromPOCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new InvalidArgumentException(sprintf("% not found!", get_class($cmd)));
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var SaveCopyFromPOOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof ApDTO) {
            throw new InvalidArgumentException("ApDTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $rootEntity = $options->getRootEntity();

        if (! $rootEntity instanceof APFromPO) {
            throw new InvalidArgumentException("Root entity not found");
        }

        try {

            $notification = new Notification();

            $companyId = $options->getCompanyId();
            $userId = $options->getUserId();

            $companyQueryRepository = new DoctrineCompanyQueryRepository($cmd->getDoctrineEM());
            $company = $companyQueryRepository->getById($companyId);

            if ($company == null) {
                $notification->addError("Company not found");
                $dto->setNotification($notification);
                return;
            }

            // ====================

            $dto->company = $companyId;
            $dto->createdBy = $userId;
            $dto->currency = $dto->getDocCurrency();
            $dto->localCurrency = $company->getDefaultCurrency();

            /**
             *
             * @var APSnapshot $snapshot ;
             * @var APDoc $rootEntity ;
             */

            $snapshot = $rootEntity->makeSnapshot();

            // important
            $editableProperties = [
                "docCurrency",
                "docNumber",
                "sapDoc",
                "docDate",
                "postingDate",
                "grDate",
                "warehouse",
                "pmtTerm",
                "remarks",
                "company"
            ];

            $snapshot = APSnapshotAssembler::updateSnapshotFieldsFromDTO($snapshot, $dto, $editableProperties);

            $sharedService = SharedServiceFactory::createForAP($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->saveFromPO($snapshot, $options, $sharedService);

            $dto->id = $rootSnapshot->getId();
            $dto->token = $rootSnapshot->getToken();

            $m = sprintf("[OK] AP # %s copied from PO and saved!", $rootSnapshot->getId());
            $notification->addSuccess($m);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
