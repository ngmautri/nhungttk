<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseTransactionDTOAssembler
{

    /**
     *
     * @return array;
     */
    public static function checkItemDTO()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryItem();
        $dto = new InventoryItem();
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
     * generete DTO File.
     */
    public static function createWarehouseTransactionDTOProperities()
    {
        $entity = new \Application\Entity\NmtInventoryMv();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    /**
     * generete DTO File.
     */
    public static function createAutoGereatedFields()
    {
        $dto = new WarehouseTransactionDTO();
        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();
        $auto_generated = array();
        foreach ($itemProperites as $property) {
            // @system_genereted
            if (preg_match('/@system_genereted/', $property->getDocComment()) == 1) {
                $auto_generated[] = $property->getName();
            }
        }
        return $auto_generated;
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtInventoryItem();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$item->" . $propertyName . ");";
        }
    }
}
