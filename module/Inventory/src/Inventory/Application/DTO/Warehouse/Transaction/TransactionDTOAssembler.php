<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

use Inventory\Domain\Warehouse\Transaction\GenericWarehouseTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionDTOAssembler
{

   /**
   * 
   * @param GenericWarehouseTransaction $obj
   * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\WarehouseTransactionDTO
   */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof GenericWarehouseTransaction)
            return null;

        $dto = new WarehouseTransactionDTO();

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
        $entity = new \Application\Entity\NmtInventoryMv();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$snapshot->" . $propertyName . ");";
        }
    }
}
