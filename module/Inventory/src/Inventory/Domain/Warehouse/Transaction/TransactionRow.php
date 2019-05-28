<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Domain\Shared\AbstractEntity;
use Inventory\Application\DTO\Warehouse\Transaction\WarehouseTransactionDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRow extends AbstractEntity
{

    protected $id;

    protected $token;

    protected $checksum;

    protected $trxDate;

    protected $trxTypeId;

    protected $flow;

    protected $quantity;

    protected $remarks;

    protected $createdOn;

    protected $isLocked;

    protected $isDraft;

    protected $isActive;

    protected $lastChangeOn;

    protected $isPreferredVendor;

    protected $vendorItemUnit;

    protected $vendorItemCode;

    protected $conversionFactor;

    protected $conversionText;

    protected $vendorUnitPrice;

    protected $pmtTermId;

    protected $deliveryTermId;

    protected $leadTime;

    protected $taxRate;

    protected $currentState;

    protected $currentStatus;

    protected $targetId;

    protected $targetClass;

    protected $sourceId;

    protected $sourceClass;

    protected $docStatus;

    protected $sysNumber;

    protected $changeOn;

    protected $changeBy;

    protected $revisionNumber;

    protected $isPosted;

    protected $actualQuantity;

    protected $transactionStatus;

    protected $stockRemarks;

    protected $transactionType;

    protected $itemSerialId;

    protected $itemBatchId;

    protected $cogsLocal;

    protected $cogsDoc;

    protected $exchangeRate;

    protected $convertedStandardQuantity;

    protected $convertedStandardUnitPrice;

    protected $convertedStockQuantity;

    protected $convertedStockUnitPrice;

    protected $convertedPurchaseQuantity;

    protected $docQuantity;

    protected $docUnitPrice;

    protected $docUnit;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalDoc;

    protected $reversalReason;

    protected $isReversable;

    protected $docType;

    protected $localUnitPrice;

    protected $reversalBlocked;

    protected $createdBy;

    protected $lastChangeBy;

    protected $item;

    protected $pr;

    protected $po;

    protected $vendorInvoice;

    protected $poRow;

    protected $grRow;

    protected $inventoryGi;

    protected $inventoryGr;

    protected $inventoryTransfer;

    protected $wh;

    protected $gr;

    protected $movement;

    protected $issueFor;

    protected $docCurrency;

    protected $localCurrency;

    protected $project;

    protected $costCenter;

    protected $docUom;

    protected $postingPeriod;

    protected $whLocation;

    protected $prRow;

    protected $vendor;

    protected $currency;

    protected $pmtMethod;

    protected $invoiceRow;

    /**
     *
     * @param WarehouseTransactionRowSnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot)
    {
        if (! $snapshot instanceof WarehouseTransactionSnapshot)
            return;
        $this->id = $snapshot->id;
        $this->token = $snapshot->token;
        $this->checksum = $snapshot->checksum;
        $this->trxDate = $snapshot->trxDate;
        $this->trxTypeId = $snapshot->trxTypeId;
        $this->flow = $snapshot->flow;
        $this->quantity = $snapshot->quantity;
        $this->remarks = $snapshot->remarks;
        $this->createdOn = $snapshot->createdOn;
        $this->isLocked = $snapshot->isLocked;
        $this->isDraft = $snapshot->isDraft;
        $this->isActive = $snapshot->isActive;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->isPreferredVendor = $snapshot->isPreferredVendor;
        $this->vendorItemUnit = $snapshot->vendorItemUnit;
        $this->vendorItemCode = $snapshot->vendorItemCode;
        $this->conversionFactor = $snapshot->conversionFactor;
        $this->conversionText = $snapshot->conversionText;
        $this->vendorUnitPrice = $snapshot->vendorUnitPrice;
        $this->pmtTermId = $snapshot->pmtTermId;
        $this->deliveryTermId = $snapshot->deliveryTermId;
        $this->leadTime = $snapshot->leadTime;
        $this->taxRate = $snapshot->taxRate;
        $this->currentState = $snapshot->currentState;
        $this->currentStatus = $snapshot->currentStatus;
        $this->targetId = $snapshot->targetId;
        $this->targetClass = $snapshot->targetClass;
        $this->sourceId = $snapshot->sourceId;
        $this->sourceClass = $snapshot->sourceClass;
        $this->docStatus = $snapshot->docStatus;
        $this->sysNumber = $snapshot->sysNumber;
        $this->changeOn = $snapshot->changeOn;
        $this->changeBy = $snapshot->changeBy;
        $this->revisionNumber = $snapshot->revisionNumber;
        $this->isPosted = $snapshot->isPosted;
        $this->actualQuantity = $snapshot->actualQuantity;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->stockRemarks = $snapshot->stockRemarks;
        $this->transactionType = $snapshot->transactionType;
        $this->itemSerialId = $snapshot->itemSerialId;
        $this->itemBatchId = $snapshot->itemBatchId;
        $this->cogsLocal = $snapshot->cogsLocal;
        $this->cogsDoc = $snapshot->cogsDoc;
        $this->exchangeRate = $snapshot->exchangeRate;
        $this->convertedStandardQuantity = $snapshot->convertedStandardQuantity;
        $this->convertedStandardUnitPrice = $snapshot->convertedStandardUnitPrice;
        $this->convertedStockQuantity = $snapshot->convertedStockQuantity;
        $this->convertedStockUnitPrice = $snapshot->convertedStockUnitPrice;
        $this->convertedPurchaseQuantity = $snapshot->convertedPurchaseQuantity;
        $this->docQuantity = $snapshot->docQuantity;
        $this->docUnitPrice = $snapshot->docUnitPrice;
        $this->docUnit = $snapshot->docUnit;
        $this->isReversed = $snapshot->isReversed;
        $this->reversalDate = $snapshot->reversalDate;
        $this->reversalDoc = $snapshot->reversalDoc;
        $this->reversalReason = $snapshot->reversalReason;
        $this->isReversable = $snapshot->isReversable;
        $this->docType = $snapshot->docType;
        $this->localUnitPrice = $snapshot->localUnitPrice;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->createdBy = $snapshot->createdBy;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->item = $snapshot->item;
        $this->pr = $snapshot->pr;
        $this->po = $snapshot->po;
        $this->vendorInvoice = $snapshot->vendorInvoice;
        $this->poRow = $snapshot->poRow;
        $this->grRow = $snapshot->grRow;
        $this->inventoryGi = $snapshot->inventoryGi;
        $this->inventoryGr = $snapshot->inventoryGr;
        $this->inventoryTransfer = $snapshot->inventoryTransfer;
        $this->wh = $snapshot->wh;
        $this->gr = $snapshot->gr;
        $this->movement = $snapshot->movement;
        $this->issueFor = $snapshot->issueFor;
        $this->docCurrency = $snapshot->docCurrency;
        $this->localCurrency = $snapshot->localCurrency;
        $this->project = $snapshot->project;
        $this->costCenter = $snapshot->costCenter;
        $this->docUom = $snapshot->docUom;
        $this->postingPeriod = $snapshot->postingPeriod;
        $this->whLocation = $snapshot->whLocation;
        $this->prRow = $snapshot->prRow;
        $this->vendor = $snapshot->vendor;
        $this->currency = $snapshot->currency;
        $this->pmtMethod = $snapshot->pmtMethod;
        $this->invoiceRow = $snapshot->invoiceRow;
    }
}