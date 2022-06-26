<?php
namespace Procure\Domain\PurchaseRequest\Definition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrDefinition
{

    public static $fields = [

        /*
         * |=============================
         * | Procure\Domain\PurchaseRequest\PRDoc
         * |
         * |=============================
         */

        "instance",
        
        /*
         * |=============================
         * | Procure\Domain\PurchaseRequest\BasePR
         * |
         * |=============================
         */
        
        "attachmentList",
        
        /*
         * |=============================
         * | Procure\Domain\PurchaseRequest\AbstractPR
         * |
         * |=============================
         */
        
        "prAutoNumber",
        "prNumber",
        "prName",
        "keywords",
        "status",
        "checksum",
        "submittedOn",
        "totalRowManual",
        "department",
        
        /*
         * |=============================
         * | Procure\Domain\GenericDoc
         * |
         * |=============================
         */
        
        "refreshed",
        "constructedFromDB",
        
        /*
         * |=============================
         * | Procure\Domain\BaseDoc
         * |
         * |=============================
         */
        
        "rowsGenerator",
        "lazyRowSnapshotCollection",
        "lazyRowSnapshotCollectionReference",
        "rowCollection",
        "pictureList",
        "totalPicture",
        "totalAttachment",
        "docRows",
        "rowIdArray",
        "rowsOutput",
        "postingYear",
        "postingMonth",
        "docYear",
        "docMonth",
        "postingPeriodFrom",
        "postingPeriodTo",
        "postingPeriodId",
        "companyName",
        "companyId",
        "companyToken",
        "companyCode",
        "vendorId",
        "vendorToken",
        "vendorAddress",
        "vendorCountry",
        "completedAPRows",
        "completedGRRows",
        "paymentTermName",
        "paymentTermCode",
        "warehouseName",
        "warehouseCode",
        "paymentMethodName",
        "paymentMethodCode",
        "incotermCode",
        "incotermName",
        "createdByName",
        "lastChangedByName",
        "totalRows",
        "totalActiveRows",
        "maxRowNumber",
        "netAmount",
        "taxAmount",
        "grossAmount",
        "discountAmount",
        "billedAmount",
        "completedRows",
        "openAPAmount",
        "docCurrencyISO",
        "localCurrencyISO",
        "docCurrencyId",
        "localCurrencyId",
        
        /*
         * |=============================
         * | Procure\Domain\AbstractDoc
         * |
         * |=============================
         */
        
        "id",
        "token",
        "vendorName",
        "invoiceNo",
        "invoiceDate",
        "currencyIso3",
        "exchangeRate",
        "remarks",
        "createdOn",
        "currentState",
        "isActive",
        "trxType",
        "lastchangeOn",
        "postingDate",
        "grDate",
        "sapDoc",
        "contractNo",
        "contractDate",
        "quotationNo",
        "quotationDate",
        "sysNumber",
        "revisionNo",
        "deliveryMode",
        "incoterm",
        "incotermPlace",
        "paymentTerm",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "docType",
        "paymentStatus",
        "totalDocValue",
        "totalDocTax",
        "totalDocDiscount",
        "totalLocalValue",
        "totalLocalTax",
        "totalLocalDiscount",
        "reversalBlocked",
        "uuid",
        "docVersion",
        "vendor",
        "pmtTerm",
        "company",
        "warehouse",
        "createdBy",
        "lastchangeBy",
        "currency",
        "paymentMethod",
        "localCurrency",
        "docCurrency",
        "incoterm2",
        "isDraft",
        "isPosted",
        "isReversed",
        "reversalDate",
        "reversalReason",
        "postingPeriod",
        "currentStatus",
        "transactionType",
        "discountRate",
        "docNumber",
        "docDate",
        "baseDocId",
        "baseDocType",
        "targetDocId",
        "targetDocType",
        "clearingDocId"
    ];

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
        "docDate",
        "submittedOn",
        "department",
        "warehouse",
        "remarks"
    ];
}
