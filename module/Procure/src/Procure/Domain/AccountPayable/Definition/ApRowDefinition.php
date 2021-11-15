<?php
namespace Procure\Domain\AccountPayable\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowDefinition
{

    public static $fields = [

        /*
         * |=============================
         * | Procure\Domain\PurchaseOrder\BaseRow
         * |
         * |=============================
         */

        "confirmedGrBalance",
        "openAPAmount",
        "billedAmount",
        "draftGrQuantity",
        "postedGrQuantity",
        "openGrBalance",
        "draftAPQuantity",
        "postedAPQuantity",
        "openAPQuantity",
        
        /*
         * |=============================
         * | Procure\Domain\GenericRow
         * |
         * |=============================
         */
        
        "docUomVO",
        "itemStandardUomVO",
        "uomPairVO",
        "docQuantityVO",
        "itemStandardQuantityVO",
        "docCurrencyVO",
        "localCurrencyVO",
        "currencyPair",
        "docUnitPriceVO",
        "docItemStandardUnitPriceVO",
        "docNetAmountVO",
        "docTaxAmountVO",
        "docGrossAmountVO",
        "localUnitPriceVO",
        "localItemStandardUnitPriceVO",
        "LocalNetAmountVO",
        "localTaxAmountVO",
        "localGrossAmountVO",
        
        /*
         * |=============================
         * | Procure\Domain\BaseRow
         * |
         * |=============================
         */
        
        "qoQuantity",
        "standardQoQuantity",
        "postedQoQuantity",
        "postedStandardQoQuantity",
        "draftPoQuantity",
        "standardPoQuantity",
        "postedPoQuantity",
        "postedStandardPoQuantity",
        "standardGrQuantity",
        "postedStandardGrQuantity",
        "draftReturnQuantity",
        "standardReturnQuantity",
        "postedReturnQuantity",
        "postedStandardReturnQuantity",
        "draftStockQrQuantity",
        "standardStockQrQuantity",
        "postedStockQrQuantity",
        "postedStandardStockQrQuantity",
        "draftStockReturnQuantity",
        "standardStockReturnQuantity",
        "postedStockReturnQuantity",
        "postedStandardStockReturnQuantity",
        "draftApQuantity",
        "standardApQuantity",
        "postedApQuantity",
        "postedStandardApQuantity",
        "constructedFromDB",
        "standardQuantity",
        "standardUnitPriceInDocCurrency",
        "standardUnitPriceInLocCurrency",
        "docQuantityObject",
        "baseUomPair",
        "docUnitPriceObject",
        "baseDocUnitPriceObject",
        "localUnitPriceObject",
        "baseLocalUnitPriceObject",
        "localStandardUnitPrice",
        "createdByName",
        "lastChangeByName",
        "glAccountName",
        "glAccountNumber",
        "glAccountType",
        "costCenterName",
        "discountAmount",
        "convertedDocQuantity",
        "convertedDocUnitPrice",
        "companyId",
        "companyToken",
        "companyName",
        "vendorId",
        "vendorToken",
        "vendorName",
        "vendorCountry",
        "docNumber",
        "docSysNumber",
        "docCurrencyISO",
        "localCurrencyISO",
        "docCurrencyId",
        "localCurrencyId",
        "docToken",
        "docId",
        "exchangeRate",
        "docDate",
        "docYear",
        "docMonth",
        "docRevisionNo",
        "docWarehouseName",
        "docWarehouseCode",
        "warehouseName",
        "warehouseCode",
        "docUomName",
        "docUomCode",
        "docUomDescription",
        "itemToken",
        "itemChecksum",
        "itemName",
        "itemName1",
        "itemSKU",
        "itemSKU1",
        "itemSKU2",
        "itemUUID",
        "itemSysNumber",
        "itemStandardUnit",
        "itemStandardUnitName",
        "itemStandardUnitCode",
        "itemVersion",
        "isInventoryItem",
        "isFixedAsset",
        "itemMonitorMethod",
        "itemModel",
        "itemSerial",
        "itemDefaultManufacturer",
        "itemManufacturerModel",
        "itemManufacturerSerial",
        "itemManufacturerCode",
        "itemKeywords",
        "itemAssetLabel",
        "itemAssetLabel1",
        "itemDescription",
        "itemInventoryGL",
        "itemCogsGL",
        "itemCostCenter",
        "pr",
        "prToken",
        "prChecksum",
        "prNumber",
        "prSysNumber",
        "prRowIndentifer",
        "prRowCode",
        "prRowName",
        "prRowConvertFactor",
        "prRowUnit",
        "prRowVersion",
        "projectId",
        "projectToken",
        "projectName",
        "prDepartment",
        "prDepartmentName",
        "prWarehouse",
        "prWarehouseName",
        "prDocId",
        "prDocToken",
        "grDocId",
        "grDocToken",
        "grSysNumber",
        "apDocId",
        "apDocToken",
        "apSysNumber",
        
        /*
         * |=============================
         * | Procure\Domain\AbstractRow
         * |
         * |=============================
         */
        
        "id",
        "rowNumber",
        "token",
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
        "createdOn",
        "lastchangeOn",
        "currentState",
        "vendorItemCode",
        "traceStock",
        "grossAmount",
        "taxAmount",
        "faRemarks",
        "rowIdentifer",
        "discountRate",
        "revisionNo",
        "targetObject",
        "sourceObject",
        "targetObjectId",
        "sourceObjectId",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "isPosted",
        "isDraft",
        "exwUnitPrice",
        "totalExwPrice",
        "convertFactorPurchase",
        "convertedPurchaseQuantity",
        "convertedStandardQuantity",
        "convertedStockQuantity",
        "convertedStandardUnitPrice",
        "convertedStockUnitPrice",
        "docQuantity",
        "docUnit",
        "docUnitPrice",
        "convertedPurchaseUnitPrice",
        "docType",
        "descriptionText",
        "vendorItemName",
        "reversalBlocked",
        "invoice",
        "lastchangeBy",
        "prRow",
        "createdBy",
        "warehouse",
        "po",
        "item",
        "docUom",
        "docVersion",
        "uuid",
        "localUnitPrice",
        "exwCurrency",
        "localNetAmount",
        "localGrossAmount",
        "transactionType",
        "isReversed",
        "reversalDate",
        "glAccount",
        "costCenter",
        "standardConvertFactor",
        "clearingDocId",
        "brand",
        "variantId",
        "project"
    ];

    public static $defaultExcludedFields = [
        "id",
        "rowNumber",
        "token",
        "createdOn",
        "lastchangeOn",
        "currentState",
        "grossAmount",
        "taxAmount",
        "faRemarks",
        "rowIdentifer",
        "revisionNo",
        "targetObject",
        "sourceObject",
        "targetObjectId",
        "sourceObjectId",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "isPosted",
        "isDraft",
        "exwUnitPrice",
        "convertFactorPurchase",
        "convertedPurchaseQuantity",
        "convertedStandardQuantity",
        "convertedStockQuantity",
        "convertedStandardUnitPrice",
        "convertedStockUnitPrice",
        "convertedPurchaseUnitPrice",
        "docType",
        "lastchangeBy",
        "createdBy",
        "warehouse",
        "docVersion",
        "uuid",
        "localNetAmount",
        "localGrossAmount",
        "transactionType",
        "isReversed",
        "reversalDate",
        "clearingDocId"
    ];

    public static $defaultIncludedFields = [
        "rowNumber",
        "quantity",
        "unitPrice",
        "netAmount",
        "unit",
        "itemUnit",
        "conversionFactor",
        "taxRate",
        "remarks",
        "isActive",
        "vendorItemCode",
        "faRemarks",
        "discountRate",
        "docQuantity",
        "docUnit",
        "docUnitPrice",
        "descriptionText",
        "vendorItemName",
        "prRow",
        "warehouse",
        "item",
        "docUom",
        "transactionType",
        "reversalDate",
        "glAccount",
        "costCenter",
        "standardConvertFactor",
        "clearingDocId",
        "brand",
        "variantId",
        "project"
    ];
}