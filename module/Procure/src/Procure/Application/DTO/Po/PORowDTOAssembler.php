<?php
namespace Procure\Application\DTO\Po;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDTOAssembler
{

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        $entity = new \Application\Entity\NmtProcurePoRow();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        return $props;
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
                $m = sprintf("\n public $%s;",$propertyName);
                echo $m;
                $missingProperties[] = $m;
                
            }
        }
        return $missingProperties;
    }
}
