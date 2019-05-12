<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Inventory\Domain\Item\Specification\InventoryItemSpecification;
use Inventory\Domain\Exception\LogicException;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InventoryItemFactory extends AbstractItemFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Factory\AbstractItemFactory::createItemFromDB()
     */
    public function createItemFromDTO($input)
    {
        $spec1 = new ItemSpecification();
        $spec2 = new InventoryItemSpecification();

        $item = new InventoryItem();

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($item, $propertyName)) {
                    $item->$propertyName = $property->getValue($input);
                }
            }
        }

        // check invariants
        $spec = $spec1->andSpec($spec2);

        if (! $spec->isSatisfiedBy($item)) {
            throw new LogicException("Can not create inventory-item");
        }

        $item->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        $item->token = $item->uuid;
        
        $item->createdOn = new \DateTime();

        return $item;
    }
}