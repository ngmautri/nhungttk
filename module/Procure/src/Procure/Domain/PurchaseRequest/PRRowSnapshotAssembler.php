<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Procure\Domain\PurchaseRequest\Definition\PrRowDefinition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRowSnapshotAssembler
{

    /*
     * |=============================
     * | Update Snapshot from Array
     * |
     * |=============================
     */
    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, PrRowDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, PrRowDefinition::$defaultExcludedFields);
    }

    /*
     * |=============================
     * | Update Snapshot from OBject
     * |
     * |=============================
     */
    public static function updateDefaultFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, PrRowDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, PrRowDefinition::$defaultExcludedFields);
    }

    /*
     * |=============================
     * | For Form Element
     * |
     * |=============================
     */
    public static function createFormElementsExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = PrRowDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsExclude($className, $properties);
    }

    public static function createFormElementsFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = PrRowDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFor($className, $properties);
    }

    /*
     * |=============================
     * | For Form Get Function
     * |
     * |=============================
     */
    public static function createFormElementsFunctionExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = PrRowDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionExclude($className, $properties);
    }

    public static function createFormElementsFunctionFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = PrRowDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionFor($className, $properties);
    }

    /**
     *
     * @param object $hit
     * @return void|\Procure\Domain\PurchaseRequest\PRRowSnapshot
     */
    public static function createFromQueryHit($hit)
    {
        if ($hit == null) {
            return;
        }

        $snapshort = new PRRowSnapshot();
        $reflectionClass = new \ReflectionClass($snapshort);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($hit->__isset($propertyName)) {
                $snapshort->$propertyName = $hit->$propertyName;
            }
        }

        $snapshort->id = $hit->rowId; // important
        if ($hit->__isset("itemId")) {
            $snapshort->item = $hit->itemId; // important
        }

        return $snapshort;
    }
}
