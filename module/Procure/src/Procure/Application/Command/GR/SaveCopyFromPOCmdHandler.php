<?php
namespace Procure\Application\Command\GR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\GR\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\PoRowValidator;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\GoodsReceipt\Validator\Row\GLAccountValidator;

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
            throw new GrCreateException(sprintf("% not found!", get_class($cmd)));
        }

        /**
         *
         * @var GrDTO $dto ;
         * @var SaveCopyFromPOOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof GrDTO) {
            throw new GrCreateException("GrDTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new GrCreateException("No Options given. Pls check command configuration!");
        }

        $rootEntity = $options->getRootEntity();

        if (! $rootEntity instanceof GRDoc) {
            throw new GrCreateException("Root entity not found");
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
             * @var GRSnapshot $snapshot ;
             * @var GRDoc $rootEntity ;
             */

            $snapshot = $rootEntity->makeSnapshot();

            // important
            $editableProperties = [
                "grDate",
                "warehouse"
            ];

            $snapshot = GRSnapshotAssembler::updateSnapshotFieldsFromDTO($snapshot, $dto, $editableProperties);

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $procureSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $sharedService = new SharedService($sharedSpecsFactory, $fxService);

            // Header validator
            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);
            $validator = new GrDateAndWarehouseValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            // Row validator
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $validator = new PoRowValidator($sharedSpecsFactory, $fxService,$procureSpecsFactory);
            $rowValidators->add($validator);
    
            $cmdRepository = new GRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new GrPostingService($cmdRepository);
             
            $rootEntity->saveFromPO($snapshot, $options , $headerValidators, $rowValidators, $sharedService, $postingService);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $m = sprintf("[OK] GR # %s saved", $dto->getId());
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
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            $notification->addError($e->getMessage());
        }

        $dto->setNotification($notification);
    }
}
