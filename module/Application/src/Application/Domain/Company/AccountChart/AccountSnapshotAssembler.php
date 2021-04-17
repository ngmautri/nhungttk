<?php
namespace Application\Domain\Company\AccountChart;

use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountSnapshotAssembler
{

    private static $defaultExcludedFields = array(
        "id",
        "uuid",
        "token",
        "checksum",
        "createdBy",
        "createdOn",
        "lastChangeOn",
        "lastChangeBy",
        "sysNumber",
        "company"
    );

    private static $defaultIncludedFields = array(
        "isActive",
        "remarks"
    );

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

    // from Object
    // =============================
    public static function updateAllFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($snapShot, $data);
    }

    public static function updateIncludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($snapShot, $data, self::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($snapShot, $data, self::$defaultExcludedFields);
    }
}
