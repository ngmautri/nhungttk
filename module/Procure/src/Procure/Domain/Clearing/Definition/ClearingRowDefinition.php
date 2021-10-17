<?php
namespace Procure\Domain\Clearing\Definition;

/**
 *
 * @author Nguyen Mau Tri - Ngmautri@gmail.com
 *        
 */
class ClearingRowDefinition
{

    public static $fields = [
        "row",
        "counterRow",
        "id",
        "token",
        "rtRow",
        "createdOn",
        "clearingStandardQuantity",
        "remarks",
        "doc",
        "prRow",
        "qoRow",
        "poRow",
        "grRow",
        "apRow",
        "createdBy",
        "rowIdentifer",
        "revisionNo",
        "docVersion",
        "lastChangeOn",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [
        "id",
        "token",
        "rtRow",
        "createdOn",
        "doc",
        "prRow",
        "qoRow",
        "poRow",
        "grRow",
        "apRow",
        "createdBy",
        "rowIdentifer",
        "revisionNo",
        "docVersion",
        "lastChangeOn",
        "lastChangeBy"
    ];

    public static $defaultIncludedFields = [
        "row",
        "counterRow",
        "clearingStandardQuantity",
        "remarks"
    ];
}
