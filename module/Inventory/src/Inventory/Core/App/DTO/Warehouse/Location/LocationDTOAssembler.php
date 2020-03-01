<?php
namespace Inventory\Application\DTO\Warehouse\Location;

use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationDTOAssembler
{

    public static function createDTOFromArray($data)
    {
        $dto = new LocationDTO();

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
     * @param GenericLocation $obj
     * @return NULL|\Inventory\Application\DTO\Warehouse\Location\LocationDTO
     */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof GenericLocation)
            return null;

        $dto = new LocationDTO();

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

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        $entity = new \Application\Entity\NmtInventoryWarehouseLocation();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

   
    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtInventoryWarehouseLocation();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$snapshot->" . $propertyName . ");";
        }
    }
    
    /**
     * generete DTO File.
     */
    public static function createGetMapping()
    {
        $entity = new \Application\Entity\NmtInventoryWarehouseLocation();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" .  "\$snapshot->" . $propertyName . "= " . "\$entity->get" . ucfirst($propertyName) . "();" ;
        }
    }
    
    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryWarehouse();
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
}
