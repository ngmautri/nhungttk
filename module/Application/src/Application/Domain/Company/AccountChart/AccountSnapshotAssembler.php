<?php
namespace Application\Domain\Company\AccountChart;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountSnapshotAssembler
{

    private static $defaultExcludedFields = [
        "id",
        "uuid",
        "token",
        "createdOn",
        "lastChangeOn",
        "version",
        "revisionno",
        "coaUuid",
        "coa",
        "createdBy",
        "lastChangeBy"
    ];

    private static $defaultIncludedFields = [
        "accountNumber",
        "accountName",
        "accountType",
        "parentAccountNumber",
        "isActive",
        "description",
        "remarks",
        "allowReconciliation",
        "hasCostCenter",
        "isClearingAccount",
        "isControlAccount",
        "manualPostingBlocked",
        "allowPosting",
        "controlFor"
    ];

    public static function updateAllFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFromArray($snapShot, $data);
    }

    public static function updateIncludedFieldsFromArray(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFromArray(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::$defaultExcludedFields);
    }

    // for Object
    // =============================
    public static function updateAllFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($data, $snapShot);
    }

    public static function updateIncludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($data, $snapShot, $fields);
    }

    public static function updateDefaultFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($data, $snapShot, self::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($data, $snapShot, $fields);
    }

    public static function updateDefaultExcludedFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($data, $snapShot, self::$defaultExcludedFields);
    }

    // ==================================
    // ===== FOR FORM
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
