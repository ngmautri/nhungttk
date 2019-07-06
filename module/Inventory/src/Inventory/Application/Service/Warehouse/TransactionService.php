<?php
namespace Inventory\Application\Service\Warehouse;

use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Application\Event\Handler\ItemCreatedEventHandler;
use Inventory\Application\Event\Handler\ItemUpdatedEventHandler;
use Inventory\Application\Event\Listener\ItemLoggingListener;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Event\ItemUpdatedEvent;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Inventory\Domain\Item\Factory\ItemFactory;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshotAssembler;
use Inventory\Domain\Warehouse\Transaction\Factory\TransactionFactory;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionRepository;
use Inventory\Domain\Event\TransactionCreatedEvent;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionService extends AbstractService
{

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
     * @return NULL|NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO
     */
    public function getHeader($trxId, $token = null)
    {
        $rep = new DoctrineTransactionRepository($this->getDoctrineEM());

        /**
         *
         * @var GenericTransaction $trx
         */
        $trx = $rep->getHeaderById($trxId, $token);

        if ($trx == null)
            return null;

        return $trx->makeDTO();
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
    public function updateHeader($itemId, $data, $userId, $trigger = null)
    {
        $notification = new Notification();

        if ($itemId == null) {
            $notification->addError("ItemId is Empty");
        }

        if ($userId == null) {
            $notification->addError("User is not identified for this action");
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrineItemRepository($this->getDoctrineEM());
            $item = $rep->getById($itemId);

            if ($item == null) {
                $notification->addError(sprintf("Item %s can not be retrieved", $itemId));
                return $notification;
            }

            /**
             *
             * @var ItemSnapshot $itemSnapshot ;
             */
            $itemSnapshot = $item->createSnapshot();
            $newItemSnapshot = clone ($itemSnapshot);

            $dto = ItemAssembler::createItemDTOFromArray($data);

            $newItemSnapshot = ItemSnapshotAssembler::updateSnapshotFromDTO($newItemSnapshot, $dto);

            $changeArray = $itemSnapshot->compare($newItemSnapshot);

            if ($changeArray == null) {
                $notification->addError("Nothing change on Item #" . $itemId);
                return $notification;
            }

            // do change
            $newItemSnapshot->lastChangeBy = $userId;
            $newItemSnapshot->lastChangeOn = new \DateTime();
            $newItemSnapshot->revisionNo ++;

            $newItem = ItemFactory::createItem($newItemSnapshot->itemTypeId);
            $newItem->makeFromSnapshot($newItemSnapshot);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $newItem->setSharedSpecificationFactory($sharedSpecificationFactory);

            // Validate
            $notification = $newItem->validate($notification);

            if ($notification->hasErrors()) {
                return $notification;
            }

            $rep->store($newItem, False);

            /*
             * $event = new ItemUpdatedEvent($newItem);
             *
             * $dispatcher = new EventDispatcher();
             * $dispatcher->addSubscriber(new ItemUpdatedEventHandler($newItem));
             * $dispatcher->dispatch(ItemUpdatedEvent::EVENT_NAME, $event);
             */

            $m = sprintf("Item #%s updated", $itemId);
            $changeOn = new \DateTime();

            $this->getEventManager()->trigger(ItemUpdatedEvent::EVENT_NAME, $trigger, array(
                'itemId' => $itemId
            ));

            $this->getEventManager()->trigger(ItemLoggingListener::ITEM_UPDATED_LOG, $trigger, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'objectId' => $itemId,
                'objectToken' => $newItem->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $userId,
                'changeOn' => $changeOn,
                'revisionNumber' => $newItem->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));

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
     * @param object $dto
     * @param int $companyId
     * @param int $userId
     * @param int $trigger
     */
    public function createRow($trxId, $token, $data, $userId, $trigger = null)
    {
        $notification = new Notification();

        $rep = new DoctrineTransactionRepository($this->getDoctrineEM());

        /**
         *
         * @var GenericTransaction $trx
         */
        $trx = $rep->getHeaderById($trxId, $token);

        if ($trx == null) {
            $notification->addError("Nothing change on Item #");
            return $notification;
        }

        $rowSnapshot = TransactionRowSnapshotAssembler::createSnapshotFromArray($data);
        $trx->addRowFromSnapshot($rowSnapshot);

        $notification = $trx->validateRow($row, $notification);

        if ($notification->hasErrors()) {
            return $notification;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrineTransactionRepository($this->getDoctrineEM());
            $trxId = $rep->storeRow($trx);
            
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
    
    public function updateRow($dto, $companyId, $userId, $trigger = null){
        
    }
}
