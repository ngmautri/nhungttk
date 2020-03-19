<?php
namespace Inventory\Domain\QuantityBill;

use Inventory\Application\DTO\Item\ItemDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BillSnapshotAssembler
{

    /**
     *
     * @return ItemSnapshot;
     */
    public static function createFromSnapshotCode()
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
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new ItemSnapshot();

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property)) {

                if ($value == null || $value == "") {
                    $snapShot->$property = null;
                } else {
                    $snapShot->$property = $value;
                }
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
    public static function updateSnapshotFromArray($snapShot, $data)
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
    public static function createSnapshotFromDTO($dto)
    {
        if (! $dto instanceof ItemDTO)
            return null;

        $snapShot = new ItemSnapshot();

        $refl = new \ReflectionObject($dto);
        $props = $refl->getProperties();

        foreach ($props as $prop) {
            try {

                $prop->setAccessible(true);
                $propertyName = $prop->getName();
                if (property_exists($snapShot, $propertyName)) {
                    $snapShot->$propertyName = $prop->getValue($dto);
                }
            } catch (\Exception $e) {}
        }
        return $snapShot;
    }

    /**
     *
     * @param \Inventory\Domain\Item\ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateSnapshotFromDTO($snapShot, $dto)
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
            "revisionNo",
            "isStocked",
            "isFixedAsset",
            "isSparepart",
            "itemTypeId"
        );

        // $dto->isSparepart;

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
     * @param GenericItem $item
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshotFrom($item)
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

                if ($property->getValue($item) == null || $property->getValue($item) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($item);
                }
            }
        }

        return $snapShot;
    }
}
