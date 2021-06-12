<?php
namespace Inventory\Domain\Item\Serial\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SerialDefinition
{

    public static $fields = [
        "id",
        "token",
        "serialNumber",
        "isActive",
        "remarks",
        "createdOn",
        "consumedOn",
        "mfgSerialNumber",
        "mfgDate",
        "lotNumber",
        "mfgWarrantyStart",
        "mfgWarrantyEnd",
        "itemName",
        "location",
        "category",
        "mfgName",
        "lastchangeOn",
        "revisionNo",
        "sysNumber",
        "serialNumber1",
        "serialNumber2",
        "serialNumber3",
        "mfgModel",
        "mfgModel1",
        "mfgModel2",
        "mfgDescription",
        "capacity",
        "erpAssetNumber",
        "erpAssetNumber1",
        "isReversed",
        "reversalDate",
        "reversalDoc",
        "reversalReason",
        "isReversable",
        "uuid",
        "createdBy",
        "lastchangeBy",
        "item",
        "serial",
        "inventoryTrx",
        "apRow",
        "grRow",
        "originCountry"
    ];

    public static $defaultExcludedFields = [];

    public static $defaultIncludedFields = [];
}
