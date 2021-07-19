<?php
namespace Procure\Domain\QuotationRequest\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QrRowDefinition
{

    public static $fields = [
        "id", // sys
        "rowNumber",
        "token", // sys
        "quantity",
        "unitPrice",
        "netAmount",
        "unit",
        "itemUnit",
        "conversionFactor",
        "converstionText",
        "taxRate",
        "remarks",
        "isActive",
        "createdOn", // sys
        "lastchangeOn", // sys
        "currentState", // sys
        "vendorItemCode",
        "traceStock", // sys
        "grossAmount",
        "taxAmount",
        "faRemarks",
        "rowIdentifer", // sys
        "discountRate",
        "revisionNo", // sys
        "targetObject", // sys
        "sourceObject", // sys
        "targetObjectId", // sys
        "sourceObjectId", // sys
        "docStatus", // sys
        "workflowStatus", // sys
        "transactionStatus", // sys
        "docQuantity",
        "docUnitPrice",
        "docUnit",
        "docUom",
        "isPosted", // sys
        "isDraft", // sys
        "docType", // sys
        "descriptionText",
        "vendorItemName",
        "reversalBlocked", // sys
        "uuid", // sys
        "docVersion", // sys
        "clearingDocId", // sys
        "standardConvertFactor",
        "convertedStandardQuantity",
        "convertedStandardUnitPrice",
        "convertedStockUnitPrice",
        "convertedStockQuantity",
        "variantId",
        "brand",
        "invoice",
        "prRow",
        "createdBy", // sys
        "warehouse",
        "lastchangeBy", // sys
        "qo", // sys
        "item"
    ];

    public static $defaultExcludedFields = [
        "id", // sys
        "token", // sys
        "createdOn", // sys
        "lastchangeOn", // sys
        "currentState", // sys
        "traceStock", // sys
        "rowIdentifer", // sys
        "revisionNo", // sys
        "targetObject", // sys
        "sourceObject", // sys
        "targetObjectId", // sys
        "sourceObjectId", // sys
        "docStatus", // sys
        "workflowStatus", // sys
        "transactionStatus", // sys
        "isPosted", // sys
        "isDraft", // sys
        "docType", // sys
        "reversalBlocked", // sys
        "uuid", // sys
        "docVersion", // sys
        "clearingDocId", // sys
        "createdBy", // sys
        "lastchangeBy", // sys
        "qo" // sys
    ];

    public static $defaultIncludedFields = [
        "rowNumber",
        "quantity",
        "unitPrice",
        "netAmount",
        "unit",
        "itemUnit",
        "conversionFactor",
        "converstionText",
        "taxRate",
        "remarks",
        "isActive",
        "vendorItemCode",
        "grossAmount",
        "taxAmount",
        "faRemarks",
        "discountRate",
        "docQuantity",
        "docUnitPrice",
        "docUnit",
        "docUom",
        "descriptionText",
        "vendorItemName",
        "standardConvertFactor",
        "convertedStandardQuantity",
        "convertedStandardUnitPrice",
        "convertedStockUnitPrice",
        "convertedStockQuantity",
        "variantId",
        "brand",
        "invoice",
        "prRow",
        "warehouse",
        "item"
    ];
}
