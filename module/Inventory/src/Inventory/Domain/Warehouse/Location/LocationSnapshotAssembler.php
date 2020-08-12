<?php
namespace Inventory\Domain\Warehouse\Location;

use Inventory\Application\DTO\Warehouse\WarehouseDTO;
use Inventory\Application\DTO\Warehouse\Location\LocationDTOAssembler;
use Inventory\Domain\Warehouse\WarehouseSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationSnapshotAssembler
{

    public static function findMissingPropsInEntity()
    {
        $missingProperties = array();
        $baseObj = new BaseLocation();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = LocationDTOAssembler::getEntity();

        foreach ($baseProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($entity, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    public static function findMissingDBPropsInBase()
    {
        $missingProperties = array();

        $entityProps = LocationDTOAssembler::createDTOProperities();
        $dto = new BaseLocation();

        foreach ($entityProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    public static function createProperities()
    {
        $entity = new LocationSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    public static function updateSnapshotFromDTO($snapShot, $dto)
    {
        if (! $dto instanceof WarehouseDTO || ! $snapShot instanceof WarehouseSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

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
            "location",
            "revisionNo"
        );

        $changeableProperties = array(
            "movementType",
            "movementDate",
            "warehouse",
            "remarks"
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

            /*
             * if (property_exists($snapShot, $propertyName) && in_array($propertyName, $changeableProperties)) {
             *
             * if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
             * $snapShot->$propertyName = null;
             * } else {
             * $snapShot->$propertyName = $property->getValue($dto);
             * }
             * }
             */
        }
        return $snapShot;
    }
}
