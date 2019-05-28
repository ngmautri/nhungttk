<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionSnapshotAssembler
{

    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new TransactionSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
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

        /**
         * Fields, that are update automatically
         *
         * @var array $excludedProperties
         */
        $excludedProperties = array(
            "id",
            "uuid",
            "token",
            "checksum",
            "createdBy",
            "createdOn",
            "lastChangeOn",
            "lastChangeBy",
            "sysNumber",
            "company",
            "itemType",
            "revisionNo"
        );

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }

    /**
     * 
     * @param unknown $obj
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof GenericTransaction)
            return null;

        $snapShot = new TransactionSnapshot();

        // should uss reflection object
        $reflectionClass = new \ReflectionObject($obj);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {

                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($obj);
                }
                
            }
        }
          
        return $snapShot;
    }
}
