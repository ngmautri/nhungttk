<?php
namespace Inventory\Domain\Item\Variant;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Definition\VariantDefinition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantSnapshotAssembler
{

    /*
     * |=============================
     * | Update Snapshot from Array
     * |
     * |=============================
     */
    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, VariantDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, VariantDefinition::$defaultExcludedFields);
    }

    /*
     * |=============================
     * | Update Snapshot from OBject
     * |
     * |=============================
     */
    public static function updateDefaultFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, VariantDefinition::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, VariantDefinition::$defaultExcludedFields);
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
            $properties = VariantDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsExclude($className, $properties);
    }

    public static function createFormElementsFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = VariantDefinition::$defaultIncludedFields;
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
            $properties = VariantDefinition::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionExclude($className, $properties);
    }

    public static function createFormElementsFunctionFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = VariantDefinition::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionFor($className, $properties);
    }
}
