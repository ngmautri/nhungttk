<?php
namespace Procure\Application\DTO\Qr;

use Procure\Application\DTO\Gr\GrRowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrRowDTOAssembler
{

    const LOCAL_ENTITY = "\Application\Entity\NmtProcureQoRow";

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        /**
         *
         * @var \Application\Entity\NmtProcureQoRow $props ;
         */
        $className = self::LOCAL_ENTITY;
        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        return $props;
    }

    public static function showEntityProperities()
    {
        $className = self::LOCAL_ENTITY;
        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();

        foreach ($props as $p) {
            print(\sprintf("\n %s", $p->getName()));
        }
    }

    public static function getEntity()
    {
        $className = self::LOCAL_ENTITY;
        $entity = new $className();
        return $entity;
    }

    /**
     * generete DTO File.
     */
    public static function createStoreMapping()
    {
        $className = self::LOCAL_ENTITY;
        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$snapshot->set%s = \$entity->get%s()", $propertyName, ucfirst($propertyName));
        }
    }

    /**
     * generete DTO File.
     */
    public static function createGetMapping()
    {
        $className = self::LOCAL_ENTITY;
        $entity = new $className();

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
        $className = self::LOCAL_ENTITY;
        $entity = new $className();

        $dto = new GrRowDTO();
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
