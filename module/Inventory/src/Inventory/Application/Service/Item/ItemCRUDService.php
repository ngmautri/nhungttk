<?php
namespace Inventory\Application\Service\Item;

use Application\Notification;
use Application\Service\AbstractService;
use Inventory\Domain\Event\ItemCreatedEvent;
use Inventory\Domain\Item\ItemType;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Domain\Item\Factory\ServiceItemFactory;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Inventory\Application\Event\Handler\ItemCreatedEventHandler;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Event\ItemUpdatedEvent;
use Inventory\Application\Event\Handler\ItemUpdatedEventHandler;
use Inventory\Domain\Item\GenericItem;
use Inventory\Application\Event\Listener\ItemLoggingListener;
use Inventory\Domain\Item\Factory\ItemFactory;
use Application\Application\Specification\Zend\ZendSpecificationFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemCRUDService extends AbstractService
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
        $rep = new DoctrineItemRepository($this->getDoctrineEM());

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
     * @param \Inventory\Application\DTO\Item\ItemDTO $dto
     * @param string $userId
     * @param boolean $isNew
     * @param string $trigger
     * @return
     */
    public function create($dto, $companyId, $userId, $trigger = null, $generateSysNumber = True)
    {
        $notification = new Notification();

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $dto->company = $companyId;
            $dto->createdBy = $userId;
            $snapshot = ItemSnapshotAssembler::createSnapshotFromDTO($dto);

            $item = ItemFactory::createItem($dto->itemTypeId);
            $item->makeFromSnapshot($snapshot);

            $sharedSpecificationFactory = new ZendSpecificationFactory($this->getDoctrineEM());
            $item->setSharedSpecificationFactory($sharedSpecificationFactory);

            $notification = $item->validate($notification);

            if ($notification->hasErrors()) {
                return $notification;
            }

            $rep = new DoctrineItemRepository($this->getDoctrineEM());
            $itemId = $rep->store($item, $generateSysNumber);

            $event = new ItemCreatedEvent($item);
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new ItemCreatedEventHandler($itemId, $this->getDoctrineEM()));
            $dispatcher->dispatch(ItemCreatedEvent::EVENT_NAME, $event);

            $m = sprintf("[OK] Item #%s", $itemId);
            $this->getEventManager()->trigger(ItemLoggingListener::ITEM_CREATED_LOG, __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $userId,
                'createdOn' => new \DateTime()
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
     * @param int $itemId
     * @param array $data
     * @param int $userId
     * @param string $trigger
     * @return \Application\Notification
     */
    public function update($itemId, $data, $userId, $trigger = null)
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
            
            var_dump($itemSnapshot);

            $dto = ItemAssembler::createItemDTOFromArray($data);

           
            

            $newItemSnapshot = ItemSnapshotAssembler::updateSnapshotFromDTO($newItemSnapshot, $dto);
         
            var_dump($newItemSnapshot);
            
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

            echo $newItem->getId();
            
            $rep->store($newItem, False);

            $event = new ItemUpdatedEvent($newItem);

            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new ItemUpdatedEventHandler($newItem));
            $dispatcher->dispatch(ItemUpdatedEvent::EVENT_NAME, $event);

            $m = "Item updated #" . $itemId;
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
}
