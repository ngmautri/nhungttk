<?php
namespace Inventory\Domain\Item\Component;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Component\Definition\ComponentDefinition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentSnapshotAssembler
{

    // =============================
    // Update Snapshot from Array
    // =============================
    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, ComponentDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, ComponentDefinition::$defaultExcludedFields);
    }

    // =============================
    // Update Object from Snapshot
    // =============================
    public static function updateDefaultFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, ComponentDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, ComponentDefinition::$defaultExcludedFields);
    }

    // ==================================
    // ===== FOR FORM ELEMENTS
    // ==================================
    public static function createFormElementsExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = ComponentDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsExclude($className, $properties);
    }

    public static function createFormElementsFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = ComponentDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFor($className, $properties);
    }

    // ==================================
    // ===== FOR FORM GET FUNCION
    // ==================================
    public static function createFormElementsFunctionExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = ComponentDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionExclude($className, $properties);
    }

    public static function createFormElementsFunctionFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = ComponentDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionFor($className, $properties);
    }
}
