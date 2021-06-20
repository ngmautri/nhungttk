<?php
namespace Application\Domain\Company\Brand\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandDefinition
{

    public static $fields = [
        "id",
        "uuid",
        "token",
        "brandName",
        "brandName1",
        "description",
        "remarks",
        "createdOn",
        "lastChangeOn",
        "isActive",
        "logo",
        "brandName2",
        "version",
        "revisionNo",
        "createdBy",
        "company",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [
        "id",
        "uuid",
        "token",
        "createdOn",
        "lastChangeOn",
        "version",
        "revisionNo",
        "createdBy",
        "company",
        "lastChangeBy"
    ];

    public static $defaultIncludedFields = [];
}
