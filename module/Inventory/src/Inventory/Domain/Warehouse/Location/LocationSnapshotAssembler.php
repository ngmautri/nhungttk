<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LocationSnapshotAssembler
{

    private static $fields = [
        "id",
        "createdOn",
        "sysNumber",
        "token",
        "lastChangeOn",
        "revisionNo",
        "remarks",
        "isSystemLocation",
        "isReturnLocation",
        "isScrapLocation",
        "isRootLocation",
        "locationName",
        "locationCode",
        "parentId",
        "locationType",
        "isActive",
        "isLocked",
        "path",
        "pathDepth",
        "hasMember",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "warehouse",
        "parentUuid",
        "parentCode"
    ];

    private static $defaultExcludedFields = [
        "id",
        "createdOn",
        "sysNumber",
        "token",
        "lastChangeOn",
        "revisionNo",
        "isSystemLocation",
        "isReturnLocation",
        "isScrapLocation",
        "isRootLocation",
        "locationName",
        "locationCode",
        "parentId",
        "locationType",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "warehouse"
    ];

    private static $defaultIncludedFields = [
        "remarks",
        "locationName",
        "locationCode",
        "locationType",
        "isActive",
        "isLocked",
        "parentCode"
    ];

    // =============================
    // Update Snapshot from Array
    // =============================
    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, self::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $target, $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($target, $source, self::$defaultExcludedFields);
    }

    // =============================
    // Update Object from Snapshot
    // =============================
    public static function updateDefaultFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($target, $source, self::$defaultIncludedFields);
    }

    public static function updateDefaultExcludedFieldsFrom($target, AbstractDTO $source)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($target, $source, self::$defaultExcludedFields);
    }

    // ==================================
    // ===== FOR FORM ELEMENTS
    // ==================================
    public static function createFormElementsExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = self::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsExclude($className, $properties);
    }

    public static function createFormElementsFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = self::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFor($className, $properties);
    }

    // ==================================
    // ===== FOR FORM GET FUNCION
    // ==================================
    public static function createFormElementsFunctionExclude($className, $properties = null)
    {
        if ($properties == null) {
            $properties = self::$defaultExcludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionExclude($className, $properties);
    }

    public static function createFormElementsFunctionFor($className, $properties = null)
    {
        if ($properties == null) {
            $properties = self::$defaultIncludedFields;
        }
        return GenericDTOAssembler::createFormElementsFunctionFor($className, $properties);
    }
}
