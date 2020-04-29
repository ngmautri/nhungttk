<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\PO\Options\SaveCopyFromQuoteOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\PurchaseOrder\Validator\DefaultHeaderValidator;
use Procure\Domain\PurchaseOrder\Validator\DefaultRowValidator;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveCopyFromQuoteCmdHandler extends AbstractCommandHandler
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
         * @var PoDTO $dto ;
         * @var SaveCopyFromQuoteOptions $options ;
         */
        $dto = $cmd->getDto();
        $options = $cmd->getOptions();

        if (! $dto instanceof PoDTO) {
            throw new InvalidArgumentException("PoDTO object not found!");
        }

        if (! $options instanceof CommandOptions) {
            throw new InvalidArgumentException("No Options given. Pls check command configuration!");
        }

        $rootEntity = $options->getRootEntity();

        if (! $rootEntity instanceof PODoc) {
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
             * @var POSnapshot $snapshot ;
             * @var PODoc $rootEntity ;
             */

            $snapshot = $rootEntity->makeSnapshot();

            // important
            $editableProperties = [
                "docCurrency",
                "docNumber",
                "docDate",
                "postingDate",
                "warehouse",
                "pmtTerm",
                "paymentMethod",
                "remarks"
            ];

            $snapshot = POSnapshotAssembler::updateSnapshotFieldsFromDTO($snapshot, $dto, $editableProperties);

            $sharedSpecsFactory = new ZendSpecificationFactory($cmd->getDoctrineEM());
            $procureSpecsFactory = new ProcureSpecificationFactory($cmd->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($cmd->getDoctrineEM());
            $sharedService = new SharedService($sharedSpecsFactory, $fxService);

            // Header validator
            $headerValidators = new HeaderValidatorCollection();
            $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService);
            $headerValidators->add($validator);

            // Row validator
            $rowValidators = new RowValidatorCollection();
            $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
            $rowValidators->add($validator);

            $cmdRepository = new POCmdRepositoryImpl($cmd->getDoctrineEM());
            $postingService = new POPostingService($cmdRepository);

            $rootSnapshot = $rootEntity->saveFromQuotation($snapshot, $options, $headerValidators, $rowValidators, $sharedService, $postingService);

            $dto->id = $rootSnapshot->getId();
            $dto->token = $rootSnapshot->getToken();

            $m = sprintf("[OK] PO # %s copied from Quote and saved!", $rootSnapshot->getId());
            $notification->addSuccess($m);

            // event dispatcher

            $dto->setNotification($notification);
        } catch (\Exception $e) {
            throw new OperationFailedException($e->getMessage());
        }
    }
}
