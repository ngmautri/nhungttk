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
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot($entityClass, $snapshotClass)
    {
        $missingProperties = array();
        $entity = new $entityClass();
        $dto = new $snapshotClass();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));

                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    public static function findMissingPropsInEntity($entityClass, $targetClass)
    {
        $missingProperties = array();
        $baseObj = new $targetClass();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = GenericDTOAssembler::getEntity($entityClass);

        foreach ($baseProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($entity, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @param string $entityClass
     * @param string $baseClass
     * @return array
     */
    public static function findMissingPropsInTargetObject($entityClass, $targetClass)
    {
        Assert::notNull($entityClass);
        Assert::notNull($targetClass);

        $missingProperties = array();

        $entityProps = GenericDTOAssembler::createDTOProperities($entityClass);
        $dto = new $targetClass();

        foreach ($entityProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
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
     * @param AbstractDTO $dto
     * @param array $excludedProperties
     * @return NULL|\Application\Domain\Shared\AbstractDTO
     */
    public static function updateSnapshotFromDTOExcludeFields(AbstractDTO $snapShot, AbstractDTO $dto, $excludedProperties)
    {
        if ($dto == null || ! $snapShot instanceof AbstractDTO || $excludedProperties == null) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }
}
