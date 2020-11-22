<?php
namespace Procure\Application\Command\Doctrine\PO;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandInterface;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Command\Options\SaveCopyFromCmdOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\SharedServiceFactory;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use Procure\Domain\QuotationRequest\QRDoc;
use Webmozart\Assert\Assert;

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
     * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
     */
    public function run(CommandInterface $cmd)
    {

        /**
         *
         * @var PoDTO $dto ;
         * @var SaveCopyFromCmdOptions $options ;
         * @var POSnapshot $snapshot ;
         * @var QRDoc $rootEntity ;
         *
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);

        $options = $cmd->getOptions();
        Assert::isInstanceOf($options, SaveCopyFromCmdOptions::class);

        $rootEntity = $options->getRootEntity();
        Assert::isInstanceOf($rootEntity, PODoc::class);

        try {

            $companyId = $options->getCompanyId();
            $userId = $options->getUserId();

            $companyQueryRepository = new DoctrineCompanyQueryRepository($cmd->getDoctrineEM());
            $company = $companyQueryRepository->getById($companyId);

            if ($company == null) {
                $cmd->addError("Company not found");
                return;
            }

            // ====================

            $dto = DTOFactory::createDTOFromArray($cmd->getData(), new PoDTO());
            $dto->company = $companyId;
            $dto->createdBy = $userId;
            $dto->currency = $dto->getDocCurrency();
            $dto->localCurrency = $company->getDefaultCurrency();

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

            $sharedService = SharedServiceFactory::createForPO($cmd->getDoctrineEM());
            $rootSnapshot = $rootEntity->saveFromQuotation($snapshot, $options, $sharedService);

            $dto->id = $rootSnapshot->getId();
            $dto->token = $rootSnapshot->getToken();
            $this->setOutput($dto);
            $m = sprintf("[OK] PO # %s copied from Quote and saved!", $rootSnapshot->getId());
            $cmd->addSuccess($m);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
