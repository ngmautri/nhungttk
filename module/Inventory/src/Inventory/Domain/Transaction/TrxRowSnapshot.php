<?php
namespace Inventory\Domain\Transaction;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxRowSnapshot extends RowSnapshot
{

    public $checksum;

    public $trxDate;

    public $trxTypeId;

    public $flow;

    public $isLocked;

    public $lastChangeOn;

    public $isPreferredVendor;

    public $vendorItemUnit;

    public $conversionText;

    public $vendorUnitPrice;

    public $pmtTermId;

    public $deliveryTermId;

    public $leadTime;

    public $currentStatus;

    public $targetId;

    public $targetClass;

    public $sourceId;

    public $sourceClass;

    public $sysNumber;

    public $changeOn;

    public $changeBy;

    public $revisionNumber;

    public $actualQuantity;

    public $stockRemarks;

    public $itemSerialId;

    public $itemBatchId;

    public $cogsLocal;

    public $cogsDoc;

    public $reversalDoc;

    public $reversalReason;

    public $isReversable;

    public $mvUuid;

    public $rowIdentifier;

    public $convertFactorPuchase;

    public $invoiceId;

    public $lastChangeBy;

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

    public $localCurrency;

    public $project;

    public $postingPeriod;

    public $whLocation;

    public $vendor;

    public $currency;

    public $pmtMethod;

    public $invoiceRow;

    /**
     *
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getTrxDate()
    {
        return $this->trxDate;
    }

    /**
     *
     * @return mixed
     */
    public function getTrxTypeId()
    {
        return $this->trxTypeId;
    }

    /**
     *
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     *
     * @return mixed
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPreferredVendor()
    {
        return $this->isPreferredVendor;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemUnit()
    {
        return $this->vendorItemUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionText()
    {
        return $this->conversionText;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorUnitPrice()
    {
        return $this->vendorUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getPmtTermId()
    {
        return $this->pmtTermId;
    }

    /**
     *
     * @return mixed
     */
    public function getDeliveryTermId()
    {
        return $this->deliveryTermId;
    }

    /**
     *
     * @return mixed
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getChangeOn()
    {
        return $this->changeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getActualQuantity()
    {
        return $this->actualQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getStockRemarks()
    {
        return $this->stockRemarks;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSerialId()
    {
        return $this->itemSerialId;
    }

    /**
     *
     * @return mixed
     */
    public function getItemBatchId()
    {
        return $this->itemBatchId;
    }

    /**
     *
     * @return mixed
     */
    public function getCogsLocal()
    {
        return $this->cogsLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getCogsDoc()
    {
        return $this->cogsDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     *
     * @return mixed
     */
    public function getMvUuid()
    {
        return $this->mvUuid;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdentifier()
    {
        return $this->rowIdentifier;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertFactorPuchase()
    {
        return $this->convertFactorPuchase;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorInvoice()
    {
        return $this->vendorInvoice;
    }

    /**
     *
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     *
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryGi()
    {
        return $this->inventoryGi;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryTransfer()
    {
        return $this->inventoryTransfer;
    }

    /**
     *
     * @return mixed
     */
    public function getWh()
    {
        return $this->wh;
    }

    /**
     *
     * @return mixed
     */
    public function getGr()
    {
        return $this->gr;
    }

    /**
     *
     * @return mixed
     */
    public function getMovement()
    {
        return $this->movement;
    }

    /**
     *
     * @return mixed
     */
    public function getIssueFor()
    {
        return $this->issueFor;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     *
     * @return mixed
     */
    public function getWhLocation()
    {
        return $this->whLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     *
     * @return mixed
     */
    public function getPmtMethod()
    {
        return $this->pmtMethod;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoiceRow()
    {
        return $this->invoiceRow;
    }

    /**
     *
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $trxDate
     */
    public function setTrxDate($trxDate)
    {
        $this->trxDate = $trxDate;
    }

    /**
     *
     * @param mixed $trxTypeId
     */
    public function setTrxTypeId($trxTypeId)
    {
        $this->trxTypeId = $trxTypeId;
    }

    /**
     *
     * @param mixed $flow
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;
    }

    /**
     *
     * @param mixed $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $isPreferredVendor
     */
    public function setIsPreferredVendor($isPreferredVendor)
    {
        $this->isPreferredVendor = $isPreferredVendor;
    }

    /**
     *
     * @param mixed $vendorItemUnit
     */
    public function setVendorItemUnit($vendorItemUnit)
    {
        $this->vendorItemUnit = $vendorItemUnit;
    }

    /**
     *
     * @param mixed $conversionText
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
    }

    /**
     *
     * @param mixed $vendorUnitPrice
     */
    public function setVendorUnitPrice($vendorUnitPrice)
    {
        $this->vendorUnitPrice = $vendorUnitPrice;
    }

    /**
     *
     * @param mixed $pmtTermId
     */
    public function setPmtTermId($pmtTermId)
    {
        $this->pmtTermId = $pmtTermId;
    }

    /**
     *
     * @param mixed $deliveryTermId
     */
    public function setDeliveryTermId($deliveryTermId)
    {
        $this->deliveryTermId = $deliveryTermId;
    }

    /**
     *
     * @param mixed $leadTime
     */
    public function setLeadTime($leadTime)
    {
        $this->leadTime = $leadTime;
    }

    /**
     *
     * @param mixed $currentStatus
     */
    public function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;
    }

    /**
     *
     * @param mixed $targetId
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     *
     * @param mixed $targetClass
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     *
     * @param mixed $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     *
     * @param mixed $sourceClass
     */
    public function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $changeOn
     */
    public function setChangeOn($changeOn)
    {
        $this->changeOn = $changeOn;
    }

    /**
     *
     * @param mixed $changeBy
     */
    public function setChangeBy($changeBy)
    {
        $this->changeBy = $changeBy;
    }

    /**
     *
     * @param mixed $revisionNumber
     */
    public function setRevisionNumber($revisionNumber)
    {
        $this->revisionNumber = $revisionNumber;
    }

    /**
     *
     * @param mixed $actualQuantity
     */
    public function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity = $actualQuantity;
    }

    /**
     *
     * @param mixed $stockRemarks
     */
    public function setStockRemarks($stockRemarks)
    {
        $this->stockRemarks = $stockRemarks;
    }

    /**
     *
     * @param mixed $itemSerialId
     */
    public function setItemSerialId($itemSerialId)
    {
        $this->itemSerialId = $itemSerialId;
    }

    /**
     *
     * @param mixed $itemBatchId
     */
    public function setItemBatchId($itemBatchId)
    {
        $this->itemBatchId = $itemBatchId;
    }

    /**
     *
     * @param mixed $cogsLocal
     */
    public function setCogsLocal($cogsLocal)
    {
        $this->cogsLocal = $cogsLocal;
    }

    /**
     *
     * @param mixed $cogsDoc
     */
    public function setCogsDoc($cogsDoc)
    {
        $this->cogsDoc = $cogsDoc;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @param mixed $isReversable
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @param mixed $mvUuid
     */
    public function setMvUuid($mvUuid)
    {
        $this->mvUuid = $mvUuid;
    }

    /**
     *
     * @param mixed $rowIdentifier
     */
    public function setRowIdentifier($rowIdentifier)
    {
        $this->rowIdentifier = $rowIdentifier;
    }

    /**
     *
     * @param mixed $convertFactorPuchase
     */
    public function setConvertFactorPuchase($convertFactorPuchase)
    {
        $this->convertFactorPuchase = $convertFactorPuchase;
    }

    /**
     *
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $vendorInvoice
     */
    public function setVendorInvoice($vendorInvoice)
    {
        $this->vendorInvoice = $vendorInvoice;
    }

    /**
     *
     * @param mixed $poRow
     */
    public function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    public function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $inventoryGi
     */
    public function setInventoryGi($inventoryGi)
    {
        $this->inventoryGi = $inventoryGi;
    }

    /**
     *
     * @param mixed $inventoryGr
     */
    public function setInventoryGr($inventoryGr)
    {
        $this->inventoryGr = $inventoryGr;
    }

    /**
     *
     * @param mixed $inventoryTransfer
     */
    public function setInventoryTransfer($inventoryTransfer)
    {
        $this->inventoryTransfer = $inventoryTransfer;
    }

    /**
     *
     * @param mixed $wh
     */
    public function setWh($wh)
    {
        $this->wh = $wh;
    }

    /**
     *
     * @param mixed $gr
     */
    public function setGr($gr)
    {
        $this->gr = $gr;
    }

    /**
     *
     * @param mixed $movement
     */
    public function setMovement($movement)
    {
        $this->movement = $movement;
    }

    /**
     *
     * @param mixed $issueFor
     */
    public function setIssueFor($issueFor)
    {
        $this->issueFor = $issueFor;
    }

    /**
     *
     * @param mixed $docCurrency
     */
    public function setDocCurrency($docCurrency)
    {
        $this->docCurrency = $docCurrency;
    }

    /**
     *
     * @param mixed $localCurrency
     */
    public function setLocalCurrency($localCurrency)
    {
        $this->localCurrency = $localCurrency;
    }

    /**
     *
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     *
     * @param mixed $postingPeriod
     */
    public function setPostingPeriod($postingPeriod)
    {
        $this->postingPeriod = $postingPeriod;
    }

    /**
     *
     * @param mixed $whLocation
     */
    public function setWhLocation($whLocation)
    {
        $this->whLocation = $whLocation;
    }

    /**
     *
     * @param mixed $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     *
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     *
     * @param mixed $pmtMethod
     */
    public function setPmtMethod($pmtMethod)
    {
        $this->pmtMethod = $pmtMethod;
    }

    /**
     *
     * @param mixed $invoiceRow
     */
    public function setInvoiceRow($invoiceRow)
    {
        $this->invoiceRow = $invoiceRow;
    }
}