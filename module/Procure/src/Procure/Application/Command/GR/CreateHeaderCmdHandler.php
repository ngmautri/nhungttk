<?php
namespace Procure\Application\Command\GR;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\GR\Options\GrCreateOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Service\FXService;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRSnapshot;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrDateAndWarehouseValidator;
use Procure\Domain\GoodsReceipt\Validator\Header\GrPostingValidator;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateHeaderCmdHandler extends AbstractCommandHandler
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
         * @var GrCreateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof GrDTO) {
            throw new GrCreateException("DTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new GrCreateException("No Options given. Pls check command configuration!");
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
             * @var GRSnapshot $rootSnapshot ;
             * @var GRDoc $rootEntity ;
             */
            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new GRSnapshot());

            $headerValidators = new HeaderValidatorCollection();

            $sharedSpecFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $validator = new DefaultHeaderValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new GrDateAndWarehouseValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $validator = new GrPostingValidator($sharedSpecFactory, $fxService);
            $headerValidators->add($validator);

            $cmdRepository = new GRCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new GrPostingService($cmdRepository);
            $sharedService = new SharedService($sharedSpecFactory, $fxService);

            $rootEntity = GRDoc::createFrom($snapshot, $options, $headerValidators, $sharedService, $postingService);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $m = sprintf("[OK] GR # %s created", $dto->getId());
            $notification->addSuccess($m);

            // event dispatcher
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($rootEntity->getRecordedEvents());
            }
            $dto->setNotification($notification);
        } catch (\Exception $e) {

            throw new OperationFailedException($e->getMessage());
        }
    }
}
