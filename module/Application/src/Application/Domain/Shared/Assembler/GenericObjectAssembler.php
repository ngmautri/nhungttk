<?php
namespace Application\Domain\Shared\Assembler;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericObjectAssembler
{

    const ALL_FIELDS = '1';

    const INCLUDE_FIEDLS = '2';

    const EXLCUDE_FIEDLS = '3';

    public static function updateAllFieldsFrom($target, $source)
    {
        return self::updateObjectFromObject($target, $source);
    }

    public static function updateIncludedFieldsFrom($target, $source, $fields)
    {
        return self::updateObjectFromObject($target, $source, $fields, self::INCLUDE_FIEDLS);
    }

    public static function updateExcludedFieldsFrom($target, $source, $fields)
    {
        return self::updateObjectFromObject($target, $source, $fields, self::EXLCUDE_FIEDLS);
    }

    public static function updateAllFieldsFromArray($target, $source)
    {
        return self::updateObjectFromArray($target, $source);
    }

    public static function updateIncludedFieldsFromArray($target, $source, $fields)
    {
        return self::updateObjectFromArray($target, $source, $fields, self::INCLUDE_FIEDLS);
    }

    public static function updateExcludedFieldsFromArray($target, $source, $fields)
    {
        return self::updateObjectFromArray($target, $source, $fields, self::EXLCUDE_FIEDLS);
    }

    /**
     *
     * @param string $fieldName
     * @param array $fields
     * @param string $mode
     * @return boolean
     */
    private static function includeField($fieldName, $fields, $mode)
    {
        if ($mode == self::ALL_FIELDS) {
            return true;
        }

        if ($mode == self::INCLUDE_FIEDLS) {
            return in_array($fieldName, $fields);
        }

        if ($mode == self::EXLCUDE_FIEDLS) {
            return ! in_array($fieldName, $fields);
        }

        return false;
    }

    /**
     *
     * @param object $target
     * @param object $source
     * @param array $fields
     * @param string $mode
     * @return object
     */
    protected static function updateObjectFromObject($target, $source, $fields = null, $mode = self::ALL_FIELDS)
    {
        Assert::object($target);
        Assert::object($source);

        if ($mode != self::ALL_FIELDS) {
            Assert::notNull($fields);
        }

        // should use reflection object
        $sourceObject = new \ReflectionObject($source);
        $sourceFields = $sourceObject->getProperties();

        $targetObject = new \ReflectionObject($target);

        foreach ($sourceFields as $sourceField) {

            $sourceField->setAccessible(true);
            $fieldName = $sourceField->getName();

            if (property_exists($target, $fieldName) && self::includeField($fieldName, $fields, $mode)) {

                $targetField = $targetObject->getProperty($fieldName);
                $targetField->setAccessible(true);

                if ($sourceField->getValue($source) == null || $sourceField->getValue($source) == "") {
                    $targetField->setValue($target, null);
                } else {
                    $targetField->setValue($target, $sourceField->getValue($source));
                }
            }
        }
        return $target;
    }

    /**
     *
     * @param object $target
     * @param array $source
     * @param array $fields
     * @param string $mode
     * @return object
     */
    protected static function updateObjectFromArray($target, $source, $fields = null, $mode = self::ALL_FIELDS)
    {
        Assert::object($target);
        Assert::isArray($source);

        if ($mode != self::ALL_FIELDS) {
            Assert::notNull($fields);
        }

        $targetObject = new \ReflectionObject($target);

        foreach ($source as $fieldName => $v) {

            if (property_exists($target, $fieldName) && self::includeField($fieldName, $fields, $mode)) {

                $targetField = $targetObject->getProperty($fieldName);
                $targetField->setAccessible(true);

                if ($v == null || $v == "") {

                    $targetField->setValue($target, null);
                } else {
                    $targetField->setValue($target, $v);
                }
            }
        }
        return $target;
    }

    /**
     *
     * @param string $class1
     * @param string $class2
     * @return array[]
     */
    public static function findMissingProps($class1, $class2)
    {
        Assert::notNull($class1);
        Assert::notNull($class2);

        $missingProperties = array();

        $reflectionClass2 = new \ReflectionClass($class2);
        $props2 = $reflectionClass2->getProperties();

        $props2Array = [];
        foreach ($props2 as $property) {
            $property->setAccessible(true);
            $props2Array[] = $property->getName();
        }

        $reflectionClass2 = new \ReflectionClass($class1);
        $props1 = $reflectionClass2->getProperties();

        foreach ($props1 as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! \in_array($propertyName, $props2Array)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @param string $className
     * @return array
     */
    public static function createProperities($className)
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

    /**
     *
     * @param string $className
     */
    public static function printAllFields($className, $modifiers = 'public')
    {
        Assert::notNull($className);
        $reflectionClass = new \ReflectionClass($className);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "$modifiers $" . $propertyName . ";";
        }
    }

    public static function printExcludedBaseFields($className, $baseClass, $modifiers = 'public')
    {
        Assert::notNull($className);
        Assert::notNull($baseClass);

        $reflectionClass = new \ReflectionClass($className);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            if ($property->class == $reflectionClass->getName() || $property->class != $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "$modifiers $" . $propertyName . ";";
            }
        }
    }
}
