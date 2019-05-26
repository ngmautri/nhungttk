<?php
namespace Inventory\Domain\Item;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Exception\InvalidArgumentException;
use ReflectionProperty;

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
    public static function createItemFromSnapshotCode()
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
     * @param \Inventory\Domain\Item\ItemSnapshot $snapShot
     * @param array $data
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateItemSnapshotFromArray($snapShot, $data)
    {
        if ($data == null || ! $snapShot instanceof ItemSnapshot)
            return null;

        $excludedProperties = array(
            "id",
            "uuid"
        );

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property) && ! in_array($property, $excludedProperties)) {
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
        if (! $dto instanceof ItemDTO)
            return null;

        $snapShot = new ItemSnapshot();

        $reflectionClass = new \ReflectionClass(get_class($dto));
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
     * @param \Inventory\Domain\Item\ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateItemSnapshotFromDTO($snapShot, $dto)
    {
        if (! $dto instanceof ItemDTO || ! $snapShot instanceof ItemSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

        $excludedProperties = array(
            "id",
            "uuid"
        );

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                if ($property->getValue($dto) !== null) {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param AbstractItem $item
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createItemSnapshotFrom($item)
    {
        if (! $item instanceof GenericItem) {
            return null;
        }

        $snapShot = new ItemSnapshot();

        // should uss reflection object
        $reflectionClass = new \ReflectionObject($item);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {

                $snapShot->$propertyName = $property->getValue($item);
            }
        }
          
        return $snapShot;
    }
}
