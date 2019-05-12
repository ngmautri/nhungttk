<?php
namespace Inventory\Domain\Item\Factory;

use Inventory\Domain\Item\InventoryItem;

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
    public function createItemFromDB($input)
    {
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

        return $item;
    }
}