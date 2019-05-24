<?php
namespace Inventory\Domain\Item;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSnapshotAssembler
{

    /**
     *
     * @return ItemSnapshot;
     */
    public static  function createItemFromSnapshotCode()
    {
        $itemSnapshot = new ItemSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$itemSnapshot->" . $propertyName . ";";
        }
    }
    
    /**
     *
     * @param array $data
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    public static function createItemSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new ItemSnapshot();

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property)) {
                $snapShot->$property = $value;
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param ItemDTO $dto
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    public static function createItemSnapshotFromDTO($dto)
    {
        if ($dto instanceof ItemDTO)
            return null;

        $snapShot = new ItemSnapshot();

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {
                $dto->$propertyName = $property->getValue($dto);
            }
        }
        return $snapShot;
    }
    
    
    /**
     *
     * @param AbstractItem $item
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    public static function createItemSnapshotFrom($item)
    {
        if ($item instanceof AbstractItem)
            return null;
            
            $snapShot = new ItemSnapshot();
            
            $reflectionClass = new \ReflectionClass($item);
            $itemProperites = $reflectionClass->getProperties();
            
            foreach ($itemProperites as $property) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                if (property_exists($snapShot, $propertyName)) {
                    $item->$propertyName = $property->getValue($item);
                }
            }
            return $snapShot;
    }
}
