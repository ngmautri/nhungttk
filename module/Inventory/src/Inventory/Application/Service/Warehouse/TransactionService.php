<?php
namespace Inventory\Application\Service\Warehouse;

use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Inventory\Application\Event\Handler\EventHandlerFactory;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;
use Inventory\Domain\Event\TransactionCreatedEvent;
use Inventory\Domain\Event\TransactionRowCreatedEvent;
use Inventory\Domain\Event\TransactionUpdatedEvent;
use Inventory\Domain\Service\TransactionPostingService;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\Factory\TransactionFactory;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionCmdRepository;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionQueryRepository;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionRepository;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionService extends AbstractService
{

    public function post($trxId, $trxToken, $trigger = null)
    {
        $notification = new Notification();

        try {

            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            $rep = new DoctrineTransactionQueryRepository($this->getDoctrineEM());
            $whQueryRep = new DoctrineWarehouseQueryRepository($this->getDoctrineEM());

            $trx = $rep->getById($trxId);
            // var_dump($trx);

            $rep = new DoctrineTransactionQueryRepository($this->getDoctrineEM());
            $trx->setQueryRepository($rep);

            $rep = new DoctrineTransactionCmdRepository($this->getDoctrineEM());
            $trx->setCmdRepository($rep);
            $trx->setWarehouseQueryRepository($whQueryRep);

            $cogsService = new \Inventory\Application\Service\Item\FIFOLayerService();
            $cogsService->setDoctrineEM($this->doctrineEM);
            $trx->setValuationService($cogsService);

            $domainSpecificationFactory = new DoctrineSpecificationFactory($this->doctrineEM);
            $trx->setDomainSpecificationFactory($domainSpecificationFactory);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->doctrineEM);
            $trx->setSharedSpecificationFactory($sharedSpecificationFactory);

            $postingService = new TransactionPostingService($rep, $whQueryRep);

            $notification = $trx->post($postingService);

            if ($notification->hasErrors()) {
                return $notification;
            }

            // event

            if (count($trx->getRecoredEvents() > 0)) {

                $dispatcher = new EventDispatcher();

                foreach ($trx->getRecoredEvents() as $event) {

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
     * @param \Inventory\Application\DTO\Item\ItemDTO $dto
     * @param string $userId
     * @param boolean $isNew
     * @param string $trigger
     * @return
     */
    public function show($itemId, $itemToken)
    {
        $rep = new DoctrineTransactionRepository($this->getDoctrineEM());

        /**
         *
         * @var GenericItem $item
         */
        $item = $rep->getById($itemId);

        if ($item == null)
            return null;

        return $item->createDTO();
    }

    /**
     *
     * @param int $trxId
     * @param string $token
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\GenericTransaction
     */
    public function getHeader($trxId, $token = null)
    {
        $rep = new DoctrineTransactionRepository($this->getDoctrineEM());
        return $rep->getHeaderById($trxId, $token);
    }

    /**
     *
     * @param \Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO $dto
     *            ;
     * @param string $userId
     * @param boolean $isNew
     * @param string $trigger
     * @return
     */
    public function createHeader($dto, $companyId, $userId, $trigger = null)
    {
        $notification = new Notification();

        $dto->company = $companyId;
        $dto->createdBy = $userId;

        $snapshot = TransactionSnapshotAssembler::createSnapshotFromDTO($dto);
        $snapshot->docStatus = \Application\Domain\Shared\Constants::DOC_STATUS_DRAFT;

        $trx = TransactionFactory::createTransaction($snapshot->movementType);
        if ($trx == null) {
            $notification->addError("Transaction type is empty or not supported. " . $snapshot->movementType);
            return $notification;
        }

        $trx->makeFromSnapshot($snapshot);

        $domainSpecificationFactory = new DoctrineSpecificationFactory($this->getDoctrineEM());
        $trx->setDomainSpecificationFactory($domainSpecificationFactory);

        $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $trx->setSharedSpecificationFactory($sharedSpecificationFactory);

        $notification = $trx->validateHeader($notification);

        if ($notification->hasErrors()) {
            return $notification;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrineTransactionRepository($this->getDoctrineEM());
            $trxId = $rep->storeHeader($trx);

            $m = sprintf("[OK] WH Transacion # %s created", $trxId);

            $this->getEventManager()->trigger(TransactionCreatedEvent::EVENT_NAME, $trigger, array(
                'trxId' => $trxId
            ));

            /*
             * $this->getEventManager()->trigger(ItemLoggingListener::ITEM_CREATED_LOG, __METHOD__, array(
             * 'priority' => \Zend\Log\Logger::INFO,
             * 'message' => $m,
             * 'createdBy' => $userId,
             * 'createdOn' => new \DateTime()
             * ));
             */

            $notification->addSuccess($m);

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
     * @param int $itemId
     * @param array $data
     * @param int $userId
     * @param string $trigger
     * @return \Application\Notification
     */
    public function updateHeader($trxId, $trxToken, $dto, $userId, $trigger = null)
    {
        $notification = new Notification();

        if ($dto == null)
            $notification->addError("Transaction is Empty");

        if ($userId == null)
            $notification->addError("User is not identified for this action");

        $rep = new DoctrineTransactionRepository($this->getDoctrineEM());
        $trx = $rep->getHeaderById($trxId, $trxToken);

        // var_dump($trxId);

        if ($trx == null)
            $notification->addError(sprintf("Transaction %s can not be retrieved or empty", $trxId . $trxToken));

        if ($notification->hasErrors())
            return $notification;

        try {

            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            /**
             *
             * @var TransactionSnapshot $snapshot ;
             */
            $snapshot = $trx->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = TransactionSnapshotAssembler::updateSnapshotFromDTO($newSnapshot, $dto);

            $changeArray = $snapshot->compare($newSnapshot);

            if ($changeArray == null) {
                $notification->addError("Nothing change on Transaction #" . $trxId);
                return $notification;
            }

            // do change
            $newSnapshot->lastChangeBy = $userId;
            $newSnapshot->lastChangeOn = new \DateTime();
            $newSnapshot->revisionNo ++;

            $newTrx = TransactionFactory::createTransaction($newSnapshot->movementType);

            if ($newTrx == null) {
                $notification->addError("Cant not create transaction type " . $newSnapshot->movementType);
                return $notification;
            }

            $newTrx->makeFromSnapshot($newSnapshot);

            $domainSpecificationFactory = new DoctrineSpecificationFactory($this->getDoctrineEM());
            $newTrx->setDomainSpecificationFactory($domainSpecificationFactory);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $newTrx->setSharedSpecificationFactory($sharedSpecificationFactory);

            $notification = $newTrx->validateHeader($notification);

            if ($notification->hasErrors()) {
                return $notification;
            }

            $rep->storeHeader($newTrx, False);

            $m = sprintf("Transaction #%s updated", $trxId);

            $this->getEventManager()->trigger(TransactionUpdatedEvent::EVENT_NAME, $trigger, array(
                'transactionId' => $trxId
            ));
            /*
             * $changeOn = new \DateTime();
             *
             * $this->getEventManager()->trigger(ItemLoggingListener::ITEM_UPDATED_LOG, $trigger, array(
             * 'priority' => \Zend\Log\Logger::INFO,
             * 'message' => $m,
             * 'objectId' => $itemId,
             * 'objectToken' => $newItem->getToken(),
             * 'changeArray' => $changeArray,
             * 'changeBy' => $userId,
             * 'changeOn' => $changeOn,
             * 'revisionNumber' => $newItem->getRevisionNo(),
             * 'changeDate' => $changeOn,
             * 'changeValidFrom' => $changeOn
             * ));
             */
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
     * @param GenericTransaction $header
     * @param TransactionRowDTO $rowDTO
     * @param int $userId
     * @param string $trigger
     * @return \Application\Notification
     */
    public function createRow(GenericTransaction $header, $rowDTO, $userId, $trigger = null)
    {
        $notification = new Notification();

        if (! $header instanceof GenericTransaction)
            $notification->addError(sprintf("Transaction header not valid! %s", ""));

        if (! $rowDTO instanceof TransactionRowDTO)
            $notification->addError(sprintf("No Row Input given! %s", ""));

        if ($userId == null)
            $notification->addError(sprintf("No user identified for this transaction %s", ""));

        if ($notification->hasErrors())
            return $notification;

        try {

            $rowDTO->createdBy = $userId;
    
            $domainSpecificationFactory = new DoctrineSpecificationFactory($this->getDoctrineEM());
            $header->setDomainSpecificationFactory($domainSpecificationFactory);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $header->setSharedSpecificationFactory($sharedSpecificationFactory);

            $rep = new DoctrineTransactionRepository($this->getDoctrineEM());
            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            $rowSnapshot = TransactionRowSnapshotAssembler::createSnapshotFromDTO($rowDTO);

            $row = $header->addRowFromSnapshot($rowSnapshot);

            $rowId = $rep->storeRow($header, $row);

            $m = sprintf("[OK] WH Transacion row # %s created", $rowId);

            $this->getEventManager()->trigger(TransactionRowCreatedEvent::EVENT_NAME, $trigger, array(
                'rowId' => $rowId
            ));

            /*
             * $this->getEventManager()->trigger(ItemLoggingListener::ITEM_CREATED_LOG, __METHOD__, array(
             * 'priority' => \Zend\Log\Logger::INFO,
             * 'message' => $m,
             * 'createdBy' => $userId,
             * 'createdOn' => new \DateTime()
             * ));
             */

            $notification->addSuccess($m);

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

    public function updateRow($dto, $companyId, $userId, $trigger = null)
    {}
}
