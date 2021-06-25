<?php
namespace Inventory\Domain\Item\Component\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentDefinition
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
        "item",
        "revisionNo",
        "version"
    ];

    public static $defaultExcludedFields = [
        "id",
        "token",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "createdOn",
        "lastChangeOn",
        "revisionNo",
        "version"
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
