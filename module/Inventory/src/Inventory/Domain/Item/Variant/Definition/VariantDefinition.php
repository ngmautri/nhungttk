<?php
namespace Inventory\Domain\Item\Variant\Definition;

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
        "lastChangeBy",
        "fullCombinedName",
        "itemName",
        "variantSku"
    ];

    public static $defaultExcludedFields = [];

    public static $defaultIncludedFields = [];
}
