<?php
namespace Inventory\Application\Service\Warehouse;

use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
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
use Inventory\Domain\Event\TransactionRowCreatedEvent;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO;
use Inventory\Domain\Warehouse\Transaction\TransactionSnapshot;
use Inventory\Domain\Event\TransactionUpdatedEvent;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseQueryRepository;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionCmdRepository;
use Inventory\Infrastructure\Doctrine\DoctrineTransactionQueryRepository;
use Inventory\Application\DTO\Warehouse\WarehouseDTO;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Infrastructure\Doctrine\DoctrineWarehouseCmdRepository;
use Inventory\Domain\Event\WarehouseCreatedEvent;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Event\WarehouseUpdatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseService extends AbstractService
{

    /**
     *
     * @param int $warehouseId
     * @param string $token
     * @return NULL|\Inventory\Domain\Warehouse\GenericWarehouse
     */
    public function getHeader($warehouseId, $token = null)
    {
        $rep = new DoctrineWarehouseQueryRepository($this->getDoctrineEM());
        return $rep->getById($warehouseId, $token);
    }

    /**
     *
     * @param \Inventory\Application\DTO\Warehouse\WarehouseDTO $dto
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

        $snapshot = WarehouseSnapshotAssembler::createSnapshotFromDTO($dto);

        $aggregate = new GenericWarehouse();
        $aggregate->makeFromSnapshot($snapshot);

        $domainSpecificationFactory = new DoctrineSpecificationFactory($this->getDoctrineEM());
        $aggregate->setDomainSpecificationFactory($domainSpecificationFactory);

        $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $aggregate->setSharedSpecificationFactory($sharedSpecificationFactory);

        $notification = $aggregate->validateHeader($notification);

        if ($notification->hasErrors()) {
            return $notification;
        }

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $rep = new DoctrineWarehouseCmdRepository($this->getDoctrineEM());
            $whId = $rep->store($aggregate);

            $m = sprintf("[OK] Warehouse # %s created", $whId);

            $this->getEventManager()->trigger(WarehouseCreatedEvent::EVENT_NAME, $trigger, array(
                'warehouseId' => $whId
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
     * @param int $id
     * @param string $token
     * @param WarehouseDTO $dto
     * @param int $userId
     * @param string $trigger
     * @return \Application\Notification
     */
    public function updateHeader($id, $token, $dto, $userId, $trigger = null)
    {
        $notification = new Notification();

        if ($dto == null)
            $notification->addError("Warehouse is Empty");

        if ($userId == null)
            $notification->addError("User is not identified for this action");

        $rep = new DoctrineWarehouseQueryRepository($this->getDoctrineEM());
        $aggregate = $rep->getById($id, $token);

        if ($aggregate == null)
            $notification->addError(sprintf("Transaction %s can not be retrieved or empty", $id . $token));

        if ($notification->hasErrors())
            return $notification;

        try {

            $this->getDoctrineEM()
                ->getConnection()
                ->beginTransaction(); // suspend auto-commit

            /**
             *
             * @var WarehouseSnapshot $snapshot ;
             */
            $snapshot = $aggregate->makeSnapshot();
            $newSnapshot = clone ($snapshot);

            $newSnapshot = WarehouseSnapshotAssembler::updateSnapshotFromDTO($newSnapshot, $dto);

            $changeArray = $snapshot->compare($newSnapshot);

            if ($changeArray == null) {
                $notification->addError("Nothing change on Warehouse #" . $id);
                return $notification;
            }

            // do change
            $newSnapshot->lastChangeBy = $userId;
            $newSnapshot->lastChangeOn = new \DateTime();
            $newSnapshot->revisionNo ++;

            $newAggregate = new GenericWarehouse();
            $newAggregate->makeFromSnapshot($newSnapshot);

            $domainSpecificationFactory = new DoctrineSpecificationFactory($this->getDoctrineEM());
            $newAggregate->setDomainSpecificationFactory($domainSpecificationFactory);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $newAggregate->setSharedSpecificationFactory($sharedSpecificationFactory);

            $notification = $newAggregate->validateHeader($notification);

            if ($notification->hasErrors()) {
                return $notification;
            }

            $cmdRep = new DoctrineWarehouseCmdRepository($this->getDoctrineEM());
            $cmdRep->store($newAggregate);

            $m = sprintf("Warehouse #%s updated", $id);

            $this->getEventManager()->trigger(WarehouseUpdatedEvent::EVENT_NAME, $trigger, array(
                'warehouseId' => $id
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
     * @param GenericWarehouse $header
     * @param WarehouseDTO $rowDTO
     * @param int $userId
     * @param string $trigger
     * @return \Application\Notification
     */
    public function createLocation(GenericWarehouse $header, $rowDTO, $userId, $trigger = null)
    {
        $notification = new Notification();

        if (! $header instanceof GenericWarehouse)
            $notification->addError(sprintf("Warehouse header not valid! %s", ""));

            if (! $rowDTO instanceof WarehouseDTO)
            $notification->addError(sprintf("No location Input given! %s", ""));

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
