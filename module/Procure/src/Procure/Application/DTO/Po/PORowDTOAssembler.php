<?php
namespace Procure\Application\DTO\Po;

use Procure\Domain\PurchaseOrder\PORow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDTOAssembler
{

    public static function createDTOFromArray($data)
    {
        $dto = new PoRowDTO();

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
     * @param PORow $obj
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public static function createDTOFrom(PORow $obj)
    {
        if (! $obj instanceof PORow)
            return null;

        $dto = new PORowDTO();

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
     *
     * @param PORow $obj
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public static function createDetailsDTOFrom(PORow $obj)
    {
        if (! $obj instanceof PORow)
            return null;
            
            $dto = new PORowDetailsDTO();
            
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
        $entity = new \Application\Entity\NmtProcurePoRow();
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
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtProcurePoRow();
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
        $entity = new \Application\Entity\NmtProcurePoRow();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$snapshot->" . $propertyName . "= " . "\$entity->get" . ucfirst($propertyName) . "();";
        }
    }

    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtProcurePoRow();
        $dto = new PORowDTO();
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
