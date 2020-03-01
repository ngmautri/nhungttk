<?php
namespace Procure\Application\Command\PO;

use Application\Notification;
use Application\Application\Command\AbstractDoctrineCmdHandler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Procure\Application\Service\FXService;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Application\DTO\Po\PoDTO;
use Application\Application\Command\AbstractDoctrineCmd;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AmendCmdHandler extends AbstractDoctrineCmdHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Command\AbstractDoctrineCmdHandler::run()
     */
    public function run(AbstractDoctrineCmd $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        if ($cmd->getDto() == null) {
            throw new \Exception("DTO object not found!");
        }
        /**
         *
         * @var PoDTO $dto ;
         */
        $dto = $cmd->getDto();
        $notification = new Notification();

        if ($cmd->getOptions() == null) {
            $notification->addError("No Options given");
        }

        $options = $cmd->getOptions();

        $companyId = null;
        if (isset($options['companyId'])) {
            $companyId = $options['$companyId'];
        } else {
            $notification->addError("Company ID not given");
        }

        $userId = null;
        if (isset($options['userId'])) {
            $userId = $options['$userId'];
        } else {
            $notification->addError("user ID not given");
        }

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        $dto->company = $companyId;
        $dto->createdBy = $userId;

        $dto->docStatus = PODocStatus::DOC_STATUS_DRAFT;

        $companyQueryRepository = new DoctrineCompanyQueryRepository($this->getDoctrineEM());
        $company = $companyQueryRepository->getById($companyId);

        if ($company == null) {
            $notification->addError("Company not found");
            $dto->setNotification($notification);
            return;
        }

        /**
         *
         * @var POSnapshot $snapshot ;
         */
        $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new POSnapshot());
        $snapshot->localCurrency = $company->getDefaultCurrency();

        $entityRoot = PODoc::makeFromSnapshot($snapshot);

        $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $fxService = new FXService();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $specService = new POSpecService($sharedSpecificationFactory, $fxService);
        $notification = $entityRoot->validateHeader($specService, $notification);

        if ($notification->hasErrors()) {
            $dto->setNotification($notification);
            return;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrinePOCmdRepository($this->getDoctrineEM());
            $rootEntityId = $rep->storeHeader($entityRoot);
            $m = sprintf("[OK] PO # %s created", $rootEntityId);
            $notification->addSuccess($m);

            $this->getDoctrineEM()
                ->getConnection()
                ->commit();
        } catch (\Exception $e) {

            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
            $this->getDoctrineEM()->close();
            $notification->addError($e->getTraceAsString());
        }

        $dto->setNotification($notification);
    }
}
