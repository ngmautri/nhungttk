<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Application\DTO\Item\ItemAssembler;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Domain\Item\Factory\ServiceItemFactory;
use Inventory\Infrastructure\Doctrine\DoctrineItemRepository;

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

            if ($dto->isStocked == 1) {
                $factory = new InventoryItemFactory();
            }

            if ($dto->itemType == "SERVICE") {
                $factory = new ServiceItemFactory();
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
