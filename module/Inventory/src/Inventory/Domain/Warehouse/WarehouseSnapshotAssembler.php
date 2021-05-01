<?php
namespace Inventory\Domain\Warehouse;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseSnapshotAssembler
{

    private static $fields = [
        "id",
        "whCode",
        "whName",
        "whAddress",
        "whContactPerson",
        "whTelephone",
        "whEmail",
        "isLocked",
        "whStatus",
        "remarks",
        "isDefault",
        "createdOn",
        "sysNumber",
        "token",
        "lastChangeOn",
        "revisionNo",
        "uuid",
        "createdBy",
        "company",
        "whCountry",
        "lastChangeBy",
        "stockkeeper",
        "whController",
        "location"
    ];

    private static $defaultExcludedFields = [
        "id",
        "createdOn",
        "sysNumber",
        "token",
        "lastChangeOn",
        "revisionNo",
        "uuid",
        "createdBy",
        "company",
        "lastChangeBy",
        "isLocked"
    ];

    private static $defaultIncludedFields = [
        "whCode",
        "whName",
        "whAddress",
        "whContactPerson",
        "whTelephone",
        "whEmail",
        // "isLocked",
        "whStatus",
        "remarks",
        "isDefault",
        "whCountry",
        "stockkeeper",
        "whController",
        "location"
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
