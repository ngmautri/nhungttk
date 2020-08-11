<?php
namespace Inventory\Application\DTO\Warehouse;

use Inventory\Domain\Warehouse\GenericWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseDTOAssembler
{

    const ROOT_ENTITY = "\Application\Entity\NmtInventoryWarehouse";

    /**
     *
     * @return array
     */
    public static function createDTOProperities()
    {
        $className = self::ROOT_ENTITY;
        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        return $props;
    }

    public static function getEntity()
    {
        $className = self::ROOT_ENTITY;
        $entity = new $className();
        return $entity;
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $className = self::ROOT_ENTITY;
        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$entity->set%s(\$snapshot->%s);", ucfirst($propertyName), $propertyName);
        }
    }

    /**
     * generete DTO File.
     */
    public static function createGetMapping()
    {
        $className = self::ROOT_ENTITY;
        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$snapshot->%s = \$entity->get%s();", $propertyName, ucfirst($propertyName));
        }
    }

    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $className = self::ROOT_ENTITY;
        $entity = new $className();

        $dto = new WarehouseDTO();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @deprecated
     * @return \Inventory\Application\DTO\Warehouse\WarehouseDTO
     */
    public static function createDTOFromArray($data)
    {
        $dto = new WarehouseDTO();

        foreach ($data as $property => $value) {
            if (property_exists($dto, $property)) {
                if ($value == null || $value == "") {
                    $dto->$property = null;
                } else {
                    $dto->$property = $value;
                }
            }
        }
        return $dto;
    }

    /**
     *
     * @deprecated
     * @param GenericWarehouse $obj
     * @return NULL|\Inventory\Application\DTO\Warehouse\WarehouseDTO
     */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof GenericWarehouse)
            return null;

        $dto = new WarehouseDTO();

        $reflectionClass = new \ReflectionClass($obj);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (property_exists($dto, $propertyName)) {
                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $dto->$propertyName = null;
                } else {
                    $dto->$propertyName = $property->getValue($obj);
                }
            }
        }

        return $dto;
    }
}
