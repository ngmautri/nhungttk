<?php
namespace Procure\Application\DTO\Gr;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrDTOAssembler
{

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        $entity = new \Application\Entity\NmtProcureGr();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
       /*  foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            //print "\n" . "public $" . $propertyName . ";";
        } */
        return $props;
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtProcureGr();
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
        $entity = new \Application\Entity\NmtProcureGr();
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
        $entity = new \Application\Entity\NmtProcureGr();
        $dto = new GrDTO();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                $missingProperties[] = $propertyName;
            }
        }
        var_dump($missingProperties);
        return $missingProperties;
    }
}
