<?php
namespace Application\Domain\Company\ItemAttribute\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttributeDefinition
{

    public static $fields = [
        "id",
        "uuid",
        "attributeCode",
        "attributeName",
        "attributeName1",
        "attributeName2",
        "combinedName",
        "createdOn",
        "lastChangeOn",
        "sysNumber",
        "version",
        "revisionNo",
        "remarks",
        "group",
        "createdBy",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [
        "id",
        "uuid",
        "createdOn",
        "lastChangeOn",
        "sysNumber",
        "version",
        "revisionNo",
        "createdBy",
        "lastChangeBy"
    ];

    public static $defaultIncludedFields = [
        "attributeCode",
        "attributeName",
        "attributeName1",
        "attributeName2",
        "combinedName",
        "remarks",
        "group"
    ];
}
