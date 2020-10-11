<?php
namespace Application\Application\Contracts;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericDTOAssembler
{

    /**
     *
     * @param string $className
     * @return array
     */
    public static function createDTOProperities($className)
    {
        Assert::notNull($className);
        $entity = new $className();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        return $props;
    }

    /**
     *
     * @param string $className
     * @return object
     */
    public static function getEntity($className)
    {
        Assert::notNull($className);
        $entity = new $className();
        return $entity;
    }

    /**
     *
     * @param string $className
     */
    public static function createStoreMapping($className)
    {
        Assert::notNull($className);

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
     *
     * @param string $className
     */
    public static function createGetMapping($className)
    {
        Assert::notNull($className);

        $entity = new $className();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print \sprintf("\n \$snapshot->%s = \$entity->get%s();", $propertyName, ucfirst($propertyName));
        }
    }
}
