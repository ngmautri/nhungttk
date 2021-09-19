<?php
namespace Procure\Domain\PurchaseRequest\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrDefinition
{

    public static $fields = [];

    public static $defaultExcludedFields = [
        "id",
        "uuid",
        "token",
        "checksum",
        "createdBy",
        "createdOn",
        "lastChangeOn",
        "lastChangeBy",
        "sysNumber",
        "company",
        "itemType",
        "revisionNo",
        "currencyIso3",
        "vendorName",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "paymentStatus",
        "paymentStatus"
    ];

    public static $defaultIncludedFields = [
        "isActive",
        "prNumber",
        "keywords",
        "submittedOn",
        "department",
        "warehouse",
        "remarks"
    ];
}
