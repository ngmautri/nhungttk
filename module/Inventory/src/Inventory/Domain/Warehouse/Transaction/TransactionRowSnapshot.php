<?php
namespace Inventory\Domain\Warehouse\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowSnapshot
{

    /**
     *
     * @system_genereted
     */
    public $id;

    /**
     *
     * @system_genereted
     */
    public $token;

    /**
     *
     * @system_genereted
     */
    public $checksum;

    public $trxDate;

    public $trxTypeId;

    /**
     *
     * @system_genereted
     */
    public $flow;

    public $quantity;

    public $remarks;

    /**
     *
     * @system_genereted
     */
    public $createdOn;

    public $isLocked;

    public $isDraft;

    public $isActive;

    /**
     *
     * @system_genereted
     */
    public $lastChangeOn;

    public $isPreferredVendor;

    public $vendorItemUnit;

    public $vendorItemCode;

    public $conversionFactor;

    public $conversionText;

    public $vendorUnitPrice;

    public $pmtTermId;

    public $deliveryTermId;

    public $leadTime;

    public $taxRate;

    public $currentState;

    public $currentStatus;

    public $targetId;

    public $targetClass;

    public $sourceId;

    public $sourceClass;

    /**
     *
     * @system_genereted
     */
    public $docStatus;

    /**
     *
     * @system_genereted
     */
    public $sysNumber;

    /**
     *
     * @system_genereted
     */
    public $changeOn;

    /**
     *
     * @system_genereted
     */
    public $changeBy;

    /**
     *
     * @system_genereted
     */
    public $revisionNumber;

    /**
     *
     * @system_genereted
     */
    public $isPosted;

    public $actualQuantity;

    public $transactionStatus;

    public $stockRemarks;

    /**
     *
     * @system_genereted
     */
    public $transactionType;

    public $itemSerialId;

    public $itemBatchId;

    /**
     *
     * @system_genereted
     */
    public $cogsLocal;

    /**
     *
     * @system_genereted
     */
    public $cogsDoc;

    /**
     *
     * @system_genereted
     */
    public $exchangeRate;

    /**
     *
     * @system_genereted
     */
    public $convertedStandardQuantity;

    /**
     *
     * @system_genereted
     */
    public $convertedStandardUnitPrice;

    /**
     *
     * @system_genereted
     */
    public $convertedStockQuantity;

    /**
     *
     * @system_genereted
     */
    public $convertedStockUnitPrice;

    /**
     *
     * @system_genereted
     */
    public $convertedPurchaseQuantity;

    public $docQuantity;

    public $docUnitPrice;

    public $docUnit;

    /**
     *
     * @system_genereted
     */
    public $isReversed;

    public $reversalDate;

    public $reversalDoc;

    public $reversalReason;

    /**
     *
     * @system_genereted
     */
    public $isReversable;

    /**
     *
     * @system_genereted
     */
    public $docType;

    public $localUnitPrice;

    /**
     *
     * @system_genereted
     */
    public $reversalBlocked;

    /**
     *
     * @system_genereted
     */
    public $createdBy;

    /**
     *
     * @system_genereted
     */
    public $lastChangeBy;

    public $item;

    public $pr;

    public $po;

    public $vendorInvoice;

    public $poRow;

    public $grRow;

    public $inventoryGi;

    public $inventoryGr;

    public $inventoryTransfer;

    public $wh;

    public $gr;

    public $movement;

    public $issueFor;

    public $docCurrency;

    /**
     *
     * @system_genereted
     */
    public $localCurrency;

    public $project;

    public $costCenter;

    public $docUom;

    public $postingPeriod;

    public $whLocation;

    public $prRow;

    /**
     *
     * @system_genereted
     */
    public $vendor;

    /**
     *
     * @system_genereted
     */
    public $currency;

    public $pmtMethod;

    public $invoiceRow;
    
    public $mvUuid;
}