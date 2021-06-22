<?php
namespace Inventory\Domain\Item\Composite\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompositeDefinition
{

    public static $fields = [
        "id",
        "token",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "createdOn",
        "lastChangeOn",
        "quantity",
        "uom",
        "price",
        "remarks",
        "hasMember",
        "parentUuid",
        "parent",
        "item"
    ];

    public static $defaultExcludedFields = [
        "id",
        "token",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "createdOn",
        "lastChangeOn"
    ];

    public static $defaultIncludedFields = [
        "quantity",
        "uom",
        "price",
        "remarks",
        "hasMember",
        "parentUuid",
        "parent",
        "item"
    ];
}
