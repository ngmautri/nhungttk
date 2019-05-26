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
    public function save($dto, $userId, $trigger = null)
    {
        $notification = new Notification();

        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $dto->createdBy = $userId;

            switch ($dto->itemTypeId) {

                case ItemType::INVENTORY_ITEM_TYPE:
                    $factory = new InventoryItemFactory();
                    break;

                case ItemType::SERVICE_ITEM_TYPE:
                    $factory = new ServiceItemFactory();
                    break;
                default:
                    $factory = new InventoryItemFactory();
                    break;
            }

            $item = $factory->createItemFromDTO($dto);

            $rep = new DoctrineItemRepository($this->getDoctrineEM());
            $itemId = $rep->store($item);

            $this->getDoctrineEM()->commit(); // now commit

            $event = new ItemCreatedEvent($item);

            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new ItemCreatedEventHandler($item));
            $dispatcher->dispatch(ItemCreatedEvent::EVENT_NAME, $event);

            $this->getEventManager()->trigger(ItemCreatedEvent::EVENT_NAME, $trigger, array(
                'itemId' => $itemId
            ));

            $this->getEventManager()->trigger("inventory.change", $trigger, array(
                'itemId' => $itemId
            ));

            $notification->addSuccess("Item created #" . $itemId);
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
                $notification->addError(sprintf("Item %s can not be retrived", $itemId));
                return $notification;
            }

            $dto = ItemAssembler::createItemDTOFromArray($data);
            // var_dump($dto);

            /**
             *
             * @var ItemSnapshot $itemSnapshot ;
             */
            $itemSnapshot = $item->createItemSnapshot();

            $newItemSnapshot = clone ($itemSnapshot);
            $newItemSnapshot = ItemSnapshotAssembler::updateItemSnapshotFromDTO($newItemSnapshot, $dto);
            // var_dump($newItemSnapshot);

            $changeArray = $itemSnapshot->compare($newItemSnapshot);

            if (count($changeArray) == 0) {
                $notification->addError("Nothing change on Item #" . $itemId);
                return $notification;
            }

            // do change
            $newItemSnapshot->lastChangeBy = $userId;
            $newItemSnapshot->lastChangeOn = new \DateTime();
            $newItemSnapshot->revisionNo ++;

            switch ($newItemSnapshot->itemTypeId) {

                case ItemType::INVENTORY_ITEM_TYPE:
                    $factory = new InventoryItemFactory();
                    break;

                case ItemType::SERVICE_ITEM_TYPE:
                    $factory = new ServiceItemFactory();
                    break;
                default:
                    $factory = new InventoryItemFactory();
                    break;
            }

            $newItem = $factory->createItemFromSnapshot($newItemSnapshot);
            $rep->store($newItem);

            $event = new ItemUpdatedEvent($newItem);

            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new ItemUpdatedEventHandler($newItem));
            $dispatcher->dispatch(ItemUpdatedEvent::EVENT_NAME, $event);

            $this->getEventManager()->trigger(ItemUpdatedEvent::EVENT_NAME, $trigger, array(
                'itemId' => $itemId
            ));
            
            $this->getEventManager()->trigger("inventory.change", $trigger, array(
                'itemId' => $itemId
            ));
            
            $notification->addSuccess("Item updated #" . $itemId);
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
