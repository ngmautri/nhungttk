<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Domain\Shared\AbstractEntity;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTOAssembler;

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
    
    protected $mvUuid;
    
    

    /**
     * @return mixed
     */
    public function getMvUuid()
    {
        return $this->mvUuid;
    }

    /**
     *
     * @param TransactionSnapshot $snapshot
     */
    public function makeSnapshot()
    {
        return TransactionRowSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO
     */
    public function makeDTO()
    {
        return TransactionRowDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param TransactionSnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot)
    {
        if (! $snapshot instanceof TransactionRowSnapshot)
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
        $this->mvUuid = $snapshot->mvUuid;
        
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * @return mixed
     */
    public function getTrxDate()
    {
        return $this->trxDate;
    }

    /**
     * @return mixed
     */
    public function getTrxTypeId()
    {
        return $this->trxTypeId;
    }

    /**
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return mixed
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @return mixed
     */
    public function getIsPreferredVendor()
    {
        return $this->isPreferredVendor;
    }

    /**
     * @return mixed
     */
    public function getVendorItemUnit()
    {
        return $this->vendorItemUnit;
    }

    /**
     * @return mixed
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * @return mixed
     */
    public function getConversionText()
    {
        return $this->conversionText;
    }

    /**
     * @return mixed
     */
    public function getVendorUnitPrice()
    {
        return $this->vendorUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getPmtTermId()
    {
        return $this->pmtTermId;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTermId()
    {
        return $this->deliveryTermId;
    }

    /**
     * @return mixed
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @return mixed
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * @return mixed
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @return mixed
     */
    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    /**
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * @return mixed
     */
    public function getChangeOn()
    {
        return $this->changeOn;
    }

    /**
     * @return mixed
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }

    /**
     * @return mixed
     */
    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    /**
     * @return mixed
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     * @return mixed
     */
    public function getActualQuantity()
    {
        return $this->actualQuantity;
    }

    /**
     * @return mixed
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * @return mixed
     */
    public function getStockRemarks()
    {
        return $this->stockRemarks;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @return mixed
     */
    public function getItemSerialId()
    {
        return $this->itemSerialId;
    }

    /**
     * @return mixed
     */
    public function getItemBatchId()
    {
        return $this->itemBatchId;
    }

    /**
     * @return mixed
     */
    public function getCogsLocal()
    {
        return $this->cogsLocal;
    }

    /**
     * @return mixed
     */
    public function getCogsDoc()
    {
        return $this->cogsDoc;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @return mixed
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     * @return mixed
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     * @return mixed
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     * @return mixed
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     * @return mixed
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * @return mixed
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * @return mixed
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * @return mixed
     */
    public function getVendorInvoice()
    {
        return $this->vendorInvoice;
    }

    /**
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     * @return mixed
     */
    public function getInventoryGi()
    {
        return $this->inventoryGi;
    }

    /**
     * @return mixed
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }

    /**
     * @return mixed
     */
    public function getInventoryTransfer()
    {
        return $this->inventoryTransfer;
    }

    /**
     * @return mixed
     */
    public function getWh()
    {
        return $this->wh;
    }

    /**
     * @return mixed
     */
    public function getGr()
    {
        return $this->gr;
    }

    /**
     * @return mixed
     */
    public function getMovement()
    {
        return $this->movement;
    }

    /**
     * @return mixed
     */
    public function getIssueFor()
    {
        return $this->issueFor;
    }

    /**
     * @return mixed
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     * @return mixed
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * @return mixed
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     * @return mixed
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     * @return mixed
     */
    public function getWhLocation()
    {
        return $this->whLocation;
    }

    /**
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getPmtMethod()
    {
        return $this->pmtMethod;
    }

    /**
     * @return mixed
     */
    public function getInvoiceRow()
    {
        return $this->invoiceRow;
    }

}