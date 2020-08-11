<?php
namespace Inventory\Domain\Warehouse;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\DTO\Warehouse\WarehouseDTO;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseSnapshotAssembler
{

    public static function findMissingPropsInEntity()
    {
        $missingProperties = array();
        $baseObj = new BaseWarehouse();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = WarehouseDTOAssembler::getEntity();

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

        $entityProps = WarehouseDTOAssembler::createDTOProperities();
        $dto = new BaseWarehouse();

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
        $entity = new WarehouseSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot()
    {
        $missingProperties = array();
        $entity = new GenericWarehouse();
        $dto = new WarehouseSnapshot();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));

                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @deprecated
     * @return ItemSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new WarehouseSnapshot();
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
     * @deprecated
     * @param array $data
     * @return \Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new WarehouseSnapshot();

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
     * @deprecated
     * @param \Inventory\Domain\Warehouse\WarehouseSnapshot $snapShot
     * @param array $data
     * @return NULL|\Inventory\Domain\Warehouse\WarehouseSnapshot
     */
    public static function updateSnapshotFromArray($snapShot, $data)
    {
        if ($data == null || ! $snapShot instanceof WarehouseSnapshot)
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
     * @deprecated
     * @param WarehouseDTO $dto
     * @return \Inventory\Domain\Warehouse\WarehouseSnapshot
     */
    public static function createSnapshotFromDTO($dto)
    {
        if (! $dto instanceof WarehouseDTO)
            return null;

        $snapShot = new WarehouseSnapshot();

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
     * @param \Inventory\Domain\Item\ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
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

    /**
     *
     * @param GenericWarehouse $obj
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof GenericWarehouse) {
            return null;
        }

        $snapShot = new WarehouseSnapshot();

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
