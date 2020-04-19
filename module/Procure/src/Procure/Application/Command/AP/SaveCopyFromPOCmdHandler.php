<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\APSnapshotAssembler;
use Procure\Domain\AccountPayable\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\AccountPayable\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\AccountPayable\Validator\Header\InvoiceAndPaymentTermValidator;
use Procure\Domain\AccountPayable\Validator\Row\DefaultRowValidator;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
         * @var SaveCopyFromPOCmd $options ;
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

        if (! $rootEntity instanceof APDoc) {
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
                "remarks"
            ];

            $snapshot = APSnapshotAssembler::updateSnapshotFieldsFromDTO($snapshot, $dto, $editableProperties);

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $procureSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $sharedService = new SharedService($sharedSpecsFactory, $fxService);

            // Header validator
            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new InvoiceAndPaymentTermValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            // Row validator
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new APCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new APPostingService($cmdRepository);

            $rootEntity->saveFromPO($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $m = sprintf("[OK] AP # %s saved from PO", $dto->getId());
            $notification->addSuccess($m);

            // event dispatcher
            if (count($rootEntity->getRecordedEvents() > 0)) {

                $dispatcher = new EventDispatcher();

                foreach ($rootEntity->getRecordedEvents() as $event) {

                    $subcribers = EventHandlerFactory::createEventHandler(get_class($event), $cmd->getDoctrineEM());

                    if (count($subcribers) > 0) {
                        foreach ($subcribers as $subcriber) {
                            $dispatcher->addSubscriber($subcriber);
                        }
                    }
                    $dispatcher->dispatch(get_class($event), $event);
                }
            }

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
