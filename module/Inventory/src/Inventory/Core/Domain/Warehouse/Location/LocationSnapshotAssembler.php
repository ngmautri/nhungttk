<?php
namespace Inventory\Domain\Warehouse\Location;

use Inventory\Application\DTO\Warehouse\Location\LocationDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationSnapshotAssembler
{

    /**
     *
     * @return LocationSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new LocationSnapshot();
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
     * @return LocationSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new LocationSnapshot();

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
     * @param LocationDTO $dto
     * @return NULL|\Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    public static function createSnapshotFromDTO(LocationDTO $dto)
    {
        if (! $dto instanceof LocationDTO)
            return null;

        $snapShot = new LocationSnapshot();

        $reflectionClass = new \ReflectionClass(get_class($dto));
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {
                $snapShot->$propertyName = $property->getValue($dto);
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param LocationDTO $snapShot
     * @param LocationSnapshot $dto
     * @return NULL|\Inventory\Application\DTO\Warehouse\Location\LocationDTO|\Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    public static function updateSnapshotFromDTO(LocationDTO $snapShot, LocationSnapshot $dto)
    {
        if (! $dto instanceof LocationDTO || ! $snapShot instanceof LocationSnapshot)
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
     * @param GenericLocation $obj
     * @return NULL|\Inventory\Domain\Warehouse\Location\LocationSnapshot
     */
    public static function createSnapshotFrom(GenericLocation $obj)
    {
        if (! $obj instanceof GenericLocation) {
            return null;
        }

        $snapShot = new LocationSnapshot();

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
