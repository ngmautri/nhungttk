<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

use Inventory\Domain\Warehouse\Transaction\TransactionRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowDTOAssembler
{

    /**
     * 
     * @param array $data
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDetailDTO
     */
    public static function createDTOFromArray($data)
    {
        if ($data == null)
            return null;

        $dto = new TransactionRowDetailDTO();

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
     * @param TransactionRow $obj
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO
     */
    public static function createDTOFrom($obj)
    {
        if (! $obj instanceof TransactionRow)
            return null;

        $dto = new TransactionRowDTO();

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
        $entity = new \Application\Entity\NmtInventoryTrx();
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
    public static function createAutoGereatedFields()
    {
        $dto = new TransactionDTO();
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

    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryTrx();
        $dto = new TransactionRowDTO();
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
     * generete Mapping.
     */
    public static function createMapping()
    {
        $entity = new \Application\Entity\NmtInventoryTrx();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$dto->" . $propertyName . "=\$entity->get" . ucfirst($propertyName) . "();";
        }
    }
}
