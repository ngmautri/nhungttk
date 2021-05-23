<?php
namespace Inventory\Domain\Item\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantDefinition
{

    public static $fields = [
        "id",
        "uuid",
        "combinedName",
        "createdOn",
        "lastChangeOn",
        "price",
        "isActive",
        "upc",
        "ean13",
        "barcode",
        "weight",
        "remarks",
        "version",
        "revisionNo",
        "cbm",
        "variantCode",
        "variantName",
        "variantAlias",
        "sysNumber",
        "item",
        "createdBy",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [];

    public static $defaultIncludedFields = [];
}
