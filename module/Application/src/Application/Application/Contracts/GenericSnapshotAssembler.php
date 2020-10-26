<?php
namespace Application\Application\Contracts;

use Application\Domain\Shared\AbstractDTO;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GenericSnapshotAssembler
{

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
     * @param AbstractDTO $snapShot
     * @param AbstractDTO $dto
     * @param array $editableProperties
     * @return NULL|\Application\Domain\Shared\AbstractDTO
     */
    public static function updateSnapshotFieldsFromDTO(AbstractDTO $snapShot, AbstractDTO $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof AbstractDTO || $editableProperties == null)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (property_exists($snapShot, $propertyName) && in_array($propertyName, $editableProperties)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param AbstractDTO $snapShot
     * @param array $data
     * @param array $editableProperties
     * @return NULL|\Application\Domain\Shared\AbstractDTO
     */
    public static function updateSnapshotFieldsFromArray(AbstractDTO $snapShot, $data, $editableProperties)
    {
        if ($data == null || ! $snapShot instanceof AbstractDTO || $editableProperties == null) {
            return null;
        }

        foreach ($data as $propertyName => $v) {

            if (property_exists($snapShot, $propertyName) && in_array($propertyName, $editableProperties)) {

                if ($v == null || $v == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $v;
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param AbstractDTO $snapShot
     * @param array $data
     * @param array $excludedProperties
     * @return NULL|\Application\Domain\Shared\AbstractDTO
     */
    public static function updateSnapshotFromArrayExcludeFields(AbstractDTO $snapShot, $data, $excludedProperties)
    {
        $excludedProperties = [
            "id"
        ];

        if ($data == null || ! $snapShot instanceof AbstractDTO || $excludedProperties == null) {
            return null;
        }

        foreach ($data as $propertyName => $v) {

            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                if ($v == null || $v == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $v;
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param AbstractDTO $snapShot
     * @param AbstractDTO $dto
     * @param array $excludedProperties
     * @return NULL|\Application\Domain\Shared\AbstractDTO
     */
    public static function updateSnapshotFromDTOExcludeFields(AbstractDTO $snapShot, AbstractDTO $dto, $excludedProperties)
    {
        if ($dto == null || ! $snapShot instanceof AbstractDTO || $excludedProperties == null) {
            return null;
        }

        foreach ($data as $propertyName => $v) {

            if (property_exists($snapShot, $propertyName) && in_array($propertyName, $editableProperties)) {

                if ($v == null || $v == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $v;
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param string $className
     */
    public static function createAllSnapshotProps($className)
    {
        Assert::notNull($className);
        $reflectionClass = new \ReflectionClass($className);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    public static function createSnapshotProps($className, $baseClass)
    {
        Assert::notNull($className);
        Assert::notNull($baseClass);

        $reflectionClass = new \ReflectionClass($className);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class != $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }
}
