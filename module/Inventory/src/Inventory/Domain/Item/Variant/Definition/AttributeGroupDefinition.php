<?php
namespace Application\Domain\Company\ItemAttribute\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttributeGroupDefinition
{

    public static $fields = [
        "id",
        "uuid",
        "groupCode",
        "groupName",
        "groupName1",
        "createdOn",
        "lastChangeOn",
        "remarks",
        "version",
        "revisionNo",
        "sysNumber",
        "parentCode",
        "canHaveLeaf",
        "isActive",
        "createdBy",
        "lastChangeBy",
        "company"
    ];

    public static $defaultExcludedFields = [
        "id",
        "uuid",
        "createdOn",
        "lastChangeOn",
        "version",
        "revisionNo",
        "sysNumber",
        "createdBy",
        "lastChangeBy",
        "company"
    ];

    public static $defaultIncludedFields = [
        "groupCode",
        "groupName",
        "groupName1",
        "remarks",
        "parentCode",
        "canHaveLeaf",
        "isActive"
    ];
}
