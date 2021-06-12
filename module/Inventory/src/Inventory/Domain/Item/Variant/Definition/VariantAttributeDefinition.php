<?php
namespace Inventory\Domain\Item\Varian\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantAttributeDefinition
{

    public static $fields = [
        "id",
        "uuid",
        "createdOn",
        "lastChangeOn",
        "remarks",
        "revisionNo",
        "attribute",
        "variant",
        "createdBy",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [];

    public static $defaultIncludedFields = [];
}
