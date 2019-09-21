<?php
namespace Procure\Application\Service\PO;

use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Event\Handler\EventHandlerFactory;
use Procure\Application\Service\PO\Output\PoRowInArray;
use Procure\Application\Service\PO\Output\PoRowInExcel;
use Procure\Application\Service\PO\Output\PoRowOutputStrategy;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecificationService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Application\Service\FXService;
use Ramsey;
use Application\Infrastructure\AggregateRepository\DoctrineCompanyQueryRepository;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;

/**
 * PO Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POService extends AbstractService
{

    private $cmdRepository;

    private $queryRepository;

    /**
     *
     * @param PoDTO $dto
     * @param int $companyId
     * @param int $userId
     * @param string $trigger
     * @return void|\Application\Notification
     */
    public function createHeader(PoDTO $dto, $companyId, $userId, $trigger = null)
    {
        $notification = new Notification();

        $dto->company = $companyId;
        $dto->createdBy = $userId;
        $dto->docStatus = PODocStatus::DOC_STATUS_DRAFT;

        $companyQueryRepository = new DoctrineCompanyQueryRepository($this->getDoctrineEM());
        $company = $companyQueryRepository->getById($companyId);

        if ($company == null) {
            $notification->addError("company not found");
            return $notification;
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

        $specService = new POSpecificationService($sharedSpecificationFactory, $fxService);

        $notification = $entityRoot->validateHeader($specService, $notification);

        if ($notification->hasErrors()) {
            return $notification;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrinePOCmdRepository($this->getDoctrineEM());
            $rootEntityId = $rep->storeHeader($entityRoot);

            /*
             * if (count($entityRoot->getRecordedEvents() > 0)) {
             *
             * $dispatcher = new EventDispatcher();
             *
             * foreach ($entityRoot->getRecordedEvents() as $event) {
             *
             * $subcribers = EventHandlerFactory::createEventHandler(get_class($event));
             *
             * if (count($subcribers) > 0) {
             * foreach ($subcribers as $subcriber) {
             * $dispatcher->addSubscriber($subcriber);
             * }
             * }
             * $dispatcher->dispatch(get_class($event), $event);
             * }
             * }
             */

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

        return $notification;
    }

    /**
     *
     * @param int $rootEntityId
     * @param PoDTO $dto
     * @param int $companyId
     * @param int $userId
     * @param string $trigger
     */
    public function updateHeader($rootEntityId, PoDTO $dto, $companyId, $userId, $trigger = null)
    {
        $notification = new Notification();

        if ($rootEntityId == null) {
            $notification->addError("PO not identified for this action");
        }

        if ($dto == null) {
            $notification->addError("DTO is Empty");
        }

        if ($userId == null) {
            $notification->addError("User is not identified for this action");
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        $rootEntity = $this->getQueryRepository()->getHeaderById($rootEntityId);

        if ($rootEntity == null) {
            $notification->addError(sprintf("PO #%s can not be retrieved or empty", $rootEntityId));
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        try {

            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            /**
             *
             * @var POSnapshot $snapshot ;
             */
            $snapshot = $rootEntity->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = POSnapshotAssembler::updateSnapshotFromDTO($dto, $newSnapshot);
            $changeArray = $snapshot->compare($newSnapshot);
            // var_dump($changeArray);

            if ($changeArray == null) {
                $notification->addError("Nothing change on PO#" . $rootEntityId);
                return $notification;
            }

            // do change
            $newSnapshot->lastChangeBy = $userId;
            $newSnapshot->lastChangeOn = new \DateTime();
            $newSnapshot->revisionNo ++;

      
            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $fxService = new FXService();
            $fxService->setDoctrineEM($this->getDoctrineEM());
            $specService = new POSpecificationService($sharedSpecificationFactory, $fxService);
            
            $newRootEntity = PODoc::updateFromSnapshot($newSnapshot, $specService);
                       

            if ($notification->hasErrors()) {
                return $notification;
            }

            $this->getCmdRepository()->storeHeader($newRootEntity);

            $m = sprintf("PO #%s updated", $rootEntityId);
            
         
            

            $notification->addSuccess($m);
            $this->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {

            $notification->addError($e->getMessage());
            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }

        return $notification;
    }

    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPODetailsById($id, $outputStrategy = null)
    {
        $po = $this->getQueryRepository()->getPODetailsById($id);

        if ($po == null) {
            return null;
        }

        $factory = null;
        switch ($outputStrategy) {
            case PoRowOutputStrategy::OUTPUT_IN_ARRAY:
                $factory = new PoRowInArray();
                break;
            case PoRowOutputStrategy::OUTPUT_IN_EXCEL:
                $factory = new PoRowInExcel();
                break;

            default:
                $factory = new PoRowInArray();
                break;
        }

        if ($factory !== null) {
            $output = $factory->createOutput($po);
            $po->setRowsOutput($output);
        }

        return $po;
    }
    
    /**
     *
     * @param int $id
     * @param int $outputStrategy
     */
    public function getPOHeaderById($id)
    {
        $po = $this->getQueryRepository()->getHeaderById($id);
        
        if ($po == null) {
            return null;
        }
        return $po->makeHeaderDTO();
    }
    

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }

    /**
     *
     * @param DoctrinePOCmdRepository $cmdRepository
     */
    public function setCmdRepository(DoctrinePOCmdRepository $cmdRepository)
    {
        $this->cmdRepository = $cmdRepository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }

    /**
     *
     * @param DoctrinePOQueryRepository $queryRepository
     */
    public function setQueryRepository(DoctrinePOQueryRepository $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }
}
