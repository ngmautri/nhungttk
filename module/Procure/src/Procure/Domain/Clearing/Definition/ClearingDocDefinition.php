<?php
namespace Procure\Domain\Clearing\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ClearingDocDefinition
{

    public static $fields = [
        "rowCollection",
        "id",
        "token",
        "createdOn",
        "version",
        "revsionsNo",
        "docType",
        "createdBy",
        "revisionNo",
        "docDate",
        "docNumber",
        "sysNumber",
        "lastChangeOn",
        "lastChangeBy"
    ];

    public static $defaultExcludedFields = [
        "id",
        "token",
        "createdOn",
        "version",
        "revsionsNo",
        "docType",
        "createdBy",
        "revisionNo",
        "sysNumber",
        "lastChangeOn",
        "lastChangeBy"
    ];

    public static $defaultIncludedFields = [
        "docDate",
        "docNumber"
    ];
}
