<?php
namespace Application\Application\DTO\Uom;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomDTOAssembler
{

    const ROOT_ENTITY = "\Application\Entity\NmtApplicationUom";

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

        $dto = new UomDTO();
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
