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
use Procure\Domain\SnapshotAssembler;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecificationService;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Procure\Application\Service\FXService;

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

    public function createHeader(PoDTO $dto, $companyId, $userId, $trigger = null)
    {
        $notification = new Notification();

        $dto->company = $companyId;
        $dto->createdBy = $userId;
        $dto->docStatus = PODocStatus::DOC_STATUS_DRAFT;

        /**
         *
         * @var POSnapshot $snapshot ;
         */
        $snapshot = SnapshotAssembler::createSnapShotFromArray($dto, new POSnapshot());
        $entityRoot = new PODoc();
        $entityRoot->makeFromSnapshot($snapshot);

        $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $fxService = new FXService();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $specService = new POSpecificationService($sharedSpecificationFactory, $fxService);

        $notification = $entityRoot->validateHeader($specService, $notification);

        if ($notification->hasErrors()) {
            return $notification;
        }

        var_dump($notification);
        return;

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrinePOCmdRepository($this->getDoctrineEM());
            $trxId = $rep->storeHeader($entityRoot);

            $m = sprintf("[OK] WH Transacion # %s created", $trxId);
            $notification->addSuccess($m);

            if (count($entityRoot->getRecordedEvents() > 0)) {

                $dispatcher = new EventDispatcher();

                foreach ($entityRoot->getRecordedEvents() as $event) {

                    $subcribers = EventHandlerFactory::createEventHandler(get_class($event));

                    if (count($subcribers) > 0) {
                        foreach ($subcribers as $subcriber) {
                            $dispatcher->addSubscriber($subcriber);
                        }
                    }
                    $dispatcher->dispatch(get_class($event), $event);
                }
            }

            $this->getDoctrineEM()
                ->getConnection()
                ->commit();
        } catch (\Exception $e) {

            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
            $this->getDoctrineEM()->close();
            $notification->addError($e->getMessage());
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
