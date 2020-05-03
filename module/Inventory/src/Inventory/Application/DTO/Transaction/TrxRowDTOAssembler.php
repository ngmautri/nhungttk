<?php
namespace Inventory\Application\DTO\Transaction;

use Inventory\Application\DTO\Warehouse\Transaction\TrxRowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxRowDTOAssembler
{

    const LOCAL_ENTITY = "\Application\Entity\NmtInventoryTrx";

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        /**
         *
         * @var \Application\Entity\NmtInventoryTrx $props ;
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
            print \sprintf("\n \$entity->set%s(\$snapshot->%s);", ucfirst($propertyName), $propertyName);
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

        $dto = new TrxRowDTO();
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
