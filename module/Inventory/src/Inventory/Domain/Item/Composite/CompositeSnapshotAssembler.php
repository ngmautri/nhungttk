<?php
namespace Inventory\Domain\Item\Composite;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Composite\Definition\CompositeDefinition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompositeSnapshotAssembler
{



    // =============================
    // Update Snapshot from Array
    // =============================
    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, CompositeDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, CompositeDefinition::$defaultExcludedFields);
    }

    // =============================
    // Update Object from Snapshot
    // =============================
    public static function updateDefaultFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, CompositeDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, CompositeDefinition::$defaultExcludedFields);
    }

    // ==================================
    // ===== FOR FORM ELEMENTS
    // ==================================
    public static function createFormElementsExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = CompositeDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsExclude($className, $properties);
    }

    public static function createFormElementsFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = CompositeDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFor($className, $properties);
    }

    // ==================================
    // ===== FOR FORM GET FUNCION
    // ==================================
    public static function createFormElementsFunctionExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = CompositeDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionExclude($className, $properties);
    }

    public static function createFormElementsFunctionFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = CompositeDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionFor($className, $properties);
    }
}
