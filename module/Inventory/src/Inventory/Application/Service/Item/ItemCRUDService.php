<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Inventory\Domain\Item\ItemType;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Domain\Item\Factory\ServiceItemFactory;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;
use Inventory\Application\DTO\Item\ItemDTO;

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
        $this->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $errors = $this->validate($dto);

            if (count($errors) > 0) {
                return $errors;
            }

            $dto->createdBy = $userId;

            switch ($dto->itemType) {

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
            $entityId = $rep->store($item);
            var_dump($entityId);

            $this->getDoctrineEM()->commit();
            
        } catch (\Exception $e) {

            echo $e->getMessage();

            $this->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }
    }

    /**
     * 
     * @param ItemDTO $dto
     * @return NULL[]|string[]
     */
    private function validate($dto)
    {
        $errors = array();

        if ($dto->itemName === '' or $dto->itemName === null) {
            $errors[] = $this->controllerPlugin->translate("Please give Item Name");
        } else {
            if (! preg_match('/^[a-zA-Z0-9._-]*$/', $dto->itemName)) {
                $errors[] = $this->controllerPlugin->translate("Item name contains invalid character");
            }
        }

        return $errors;
    }
}
