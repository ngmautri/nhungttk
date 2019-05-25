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
    public function save($dto, $userId, $isNew = FALSE, $trigger = null)
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
            // var_dump($factory);

            $item = $factory->createItemFromDTO($dto);
            //var_dump($item);

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

            $notification->addSuccess("Item created" . $itemId);
        } catch (\Exception $e) {

            $notification->addError($e->getMessage());
            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }

        return $notification;
    }
}
