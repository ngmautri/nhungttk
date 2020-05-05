<?php
namespace Inventory\Domain\Item;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\DTO\Item\ItemDTOAssembler;
use Procure\Domain\GenericDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;

    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot()
    {
        $missingProperties = array();
        $entity = new GenericDoc();
        $dto = new ItemSnapshot();

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

    public static function findMissingPropsInEntity()
    {
        $missingProperties = array();
        $baseObj = new BaseItem();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = ItemDTOAssembler::getEntity();

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
     * @return array;
     */
    public static function findMissingPropsInBaseItem()
    {
        $missingProperties = array();

        $entityProps = ItemDTOAssembler::createDTOProperities();
        $dto = new BaseItem();

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
     * @param ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @param array $editableProperties
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(ItemSnapshot $snapShot, ItemDTO $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof ItemSnapshot || $editableProperties == null)
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
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new ItemSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }
}
