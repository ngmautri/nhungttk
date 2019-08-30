<?php
namespace Procure\Application\DTO\Pr;



use Procure\Domain\PurchaseRequest\PRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrRowDTOAssembler
{
    
    public static function createDTOFromArray($data)
    {
        $dto = new PrRowDTO();
        
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
     * @param PRRow $obj
     * @return NULL|\Procure\Application\DTO\Pr\PrRowDTO
     */
    public static function createDTOFrom(PRRow $obj)
    {
        if (! $obj instanceof PRRow)
            return null;
            
            $dto = new PrRowDTO();
            
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
        $entity = new \Application\Entity\NmtProcurePrRow();
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
        $entity = new \Application\Entity\NmtProcurePrRow();
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
        $entity = new \Application\Entity\NmtProcurePrRow();
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
        $entity = new \Application\Entity\NmtProcurePrRow();
        $dto = new PrRowDTO();
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
