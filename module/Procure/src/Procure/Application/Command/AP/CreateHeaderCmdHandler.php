<?php
namespace Procure\Application\Command\AP;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\AP\Options\ApCreateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APSnapshot;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;

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
            throw new InvalidArgumentException(sprintf("% not found!", get_class($cmd)));
        }

        /**
         *
         * @var ApDTO $dto ;
         * @var ApCreateOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof ApDTO) {
            throw new InvalidArgumentException("DTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
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
             * @var APSnapshot $rootSnapshot ;
             * @var APDoc $rootEntity ;
             */
            $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new APSnapshot());

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $postingService = new APPostingService(new APCmdRepositoryImpl($cmd->getDoctrineEM()));
            $fxService = new FXServiceImpl();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());

            $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
            $domainSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $sharedService->setDomainSpecificationFactory($domainSpecsFactory);

            $validationService = ValidatorFactory::createForHeader($sharedService);

            $rootEntity = APDoc::createFrom($snapshot, $options, $validationService, $sharedService);

            $dto->id = $rootEntity->getId();
            $dto->token = $rootEntity->getToken();

            $m = sprintf("[OK] AP # %s created", $dto->getId());
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
