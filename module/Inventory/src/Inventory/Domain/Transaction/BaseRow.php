<?php
namespace Inventory\Domain\Transaction;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseRow extends GenericRow
{

    // Specific Attribute, that are missing on Generic Row.
    // ===================
    protected $checksum;

    protected $trxDate;

    protected $trxTypeId;

    protected $flow;

    protected $isLocked;

    protected $lastChangeOn;

    protected $isPreferredVendor;

    protected $vendorItemUnit;

    protected $conversionText;

    protected $vendorUnitPrice;

    protected $pmtTermId;

    protected $deliveryTermId;

    protected $leadTime;

    protected $currentStatus;

    protected $targetId;

    protected $targetClass;

    protected $sourceId;

    protected $sourceClass;

    protected $sysNumber;

    protected $changeOn;

    protected $changeBy;

    protected $revisionNumber;

    protected $actualQuantity;

    protected $stockRemarks;

    protected $itemSerialId;

    protected $itemBatchId;

    protected $cogsLocal;

    protected $cogsDoc;

    protected $reversalDoc;

    protected $reversalReason;

    protected $isReversable;

    protected $mvUuid;

    protected $rowIdentifier;

    protected $convertFactorPuchase;

    protected $invoiceId;

    protected $lastChangeBy;

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

    protected $postingPeriod;

    protected $whLocation;

    protected $vendor;

    protected $currency;

    protected $pmtMethod;

    protected $invoiceRow;

    protected $stockQty;

    protected $stockValue;

    /**
     *
     * @param mixed $checksum
     */
    protected function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     *
     * @param mixed $trxDate
     */
    protected function setTrxDate($trxDate)
    {
        $this->trxDate = $trxDate;
    }

    /**
     *
     * @param mixed $trxTypeId
     */
    protected function setTrxTypeId($trxTypeId)
    {
        $this->trxTypeId = $trxTypeId;
    }

    /**
     *
     * @param mixed $flow
     */
    protected function setFlow($flow)
    {
        $this->flow = $flow;
    }

    /**
     *
     * @param mixed $isLocked
     */
    protected function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $isPreferredVendor
     */
    protected function setIsPreferredVendor($isPreferredVendor)
    {
        $this->isPreferredVendor = $isPreferredVendor;
    }

    /**
     *
     * @param mixed $vendorItemUnit
     */
    protected function setVendorItemUnit($vendorItemUnit)
    {
        $this->vendorItemUnit = $vendorItemUnit;
    }

    /**
     *
     * @param mixed $conversionText
     */
    protected function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
    }

    /**
     *
     * @param mixed $vendorUnitPrice
     */
    protected function setVendorUnitPrice($vendorUnitPrice)
    {
        $this->vendorUnitPrice = $vendorUnitPrice;
    }

    /**
     *
     * @param mixed $pmtTermId
     */
    protected function setPmtTermId($pmtTermId)
    {
        $this->pmtTermId = $pmtTermId;
    }

    /**
     *
     * @param mixed $deliveryTermId
     */
    protected function setDeliveryTermId($deliveryTermId)
    {
        $this->deliveryTermId = $deliveryTermId;
    }

    /**
     *
     * @param mixed $leadTime
     */
    protected function setLeadTime($leadTime)
    {
        $this->leadTime = $leadTime;
    }

    /**
     *
     * @param mixed $currentStatus
     */
    protected function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;
    }

    /**
     *
     * @param mixed $targetId
     */
    protected function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     *
     * @param mixed $targetClass
     */
    protected function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     *
     * @param mixed $sourceId
     */
    protected function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     *
     * @param mixed $sourceClass
     */
    protected function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $changeOn
     */
    protected function setChangeOn($changeOn)
    {
        $this->changeOn = $changeOn;
    }

    /**
     *
     * @param mixed $changeBy
     */
    protected function setChangeBy($changeBy)
    {
        $this->changeBy = $changeBy;
    }

    /**
     *
     * @param mixed $revisionNumber
     */
    protected function setRevisionNumber($revisionNumber)
    {
        $this->revisionNumber = $revisionNumber;
    }

    /**
     *
     * @param mixed $actualQuantity
     */
    protected function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity = $actualQuantity;
    }

    /**
     *
     * @param mixed $stockRemarks
     */
    protected function setStockRemarks($stockRemarks)
    {
        $this->stockRemarks = $stockRemarks;
    }

    /**
     *
     * @param mixed $itemSerialId
     */
    protected function setItemSerialId($itemSerialId)
    {
        $this->itemSerialId = $itemSerialId;
    }

    /**
     *
     * @param mixed $itemBatchId
     */
    protected function setItemBatchId($itemBatchId)
    {
        $this->itemBatchId = $itemBatchId;
    }

    /**
     *
     * @param mixed $cogsLocal
     */
    protected function setCogsLocal($cogsLocal)
    {
        $this->cogsLocal = $cogsLocal;
    }

    /**
     *
     * @param mixed $cogsDoc
     */
    protected function setCogsDoc($cogsDoc)
    {
        $this->cogsDoc = $cogsDoc;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @param mixed $isReversable
     */
    protected function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @param mixed $mvUuid
     */
    protected function setMvUuid($mvUuid)
    {
        $this->mvUuid = $mvUuid;
    }

    /**
     *
     * @param mixed $rowIdentifier
     */
    protected function setRowIdentifier($rowIdentifier)
    {
        $this->rowIdentifier = $rowIdentifier;
    }

    /**
     *
     * @param mixed $convertFactorPuchase
     */
    protected function setConvertFactorPuchase($convertFactorPuchase)
    {
        $this->convertFactorPuchase = $convertFactorPuchase;
    }

    /**
     *
     * @param mixed $invoiceId
     */
    protected function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $vendorInvoice
     */
    protected function setVendorInvoice($vendorInvoice)
    {
        $this->vendorInvoice = $vendorInvoice;
    }

    /**
     *
     * @param mixed $poRow
     */
    protected function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    protected function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $inventoryGi
     */
    protected function setInventoryGi($inventoryGi)
    {
        $this->inventoryGi = $inventoryGi;
    }

    /**
     *
     * @param mixed $inventoryGr
     */
    protected function setInventoryGr($inventoryGr)
    {
        $this->inventoryGr = $inventoryGr;
    }

    /**
     *
     * @param mixed $inventoryTransfer
     */
    protected function setInventoryTransfer($inventoryTransfer)
    {
        $this->inventoryTransfer = $inventoryTransfer;
    }

    /**
     *
     * @param mixed $wh
     */
    protected function setWh($wh)
    {
        $this->wh = $wh;
    }

    /**
     *
     * @param mixed $gr
     */
    protected function setGr($gr)
    {
        $this->gr = $gr;
    }

    /**
     *
     * @param mixed $movement
     */
    protected function setMovement($movement)
    {
        $this->movement = $movement;
    }

    /**
     *
     * @param mixed $issueFor
     */
    protected function setIssueFor($issueFor)
    {
        $this->issueFor = $issueFor;
    }

    /**
     *
     * @param mixed $docCurrency
     */
    protected function setDocCurrency($docCurrency)
    {
        $this->docCurrency = $docCurrency;
    }

    /**
     *
     * @param mixed $localCurrency
     */
    protected function setLocalCurrency($localCurrency)
    {
        $this->localCurrency = $localCurrency;
    }

    /**
     *
     * @param mixed $project
     */
    protected function setProject($project)
    {
        $this->project = $project;
    }

    /**
     *
     * @param mixed $postingPeriod
     */
    protected function setPostingPeriod($postingPeriod)
    {
        $this->postingPeriod = $postingPeriod;
    }

    /**
     *
     * @param mixed $whLocation
     */
    protected function setWhLocation($whLocation)
    {
        $this->whLocation = $whLocation;
    }

    /**
     *
     * @param mixed $vendor
     */
    protected function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     *
     * @param mixed $currency
     */
    protected function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     *
     * @param mixed $pmtMethod
     */
    protected function setPmtMethod($pmtMethod)
    {
        $this->pmtMethod = $pmtMethod;
    }

    /**
     *
     * @param mixed $invoiceRow
     */
    protected function setInvoiceRow($invoiceRow)
    {
        $this->invoiceRow = $invoiceRow;
    }

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
     * @return mixed
     */
    public function getStockQty()
    {
        return $this->stockQty;
    }

    /**
     *
     * @return mixed
     */
    public function getStockValue()
    {
        return $this->stockValue;
    }

    /**
     *
     * @param mixed $stockQty
     */
    public function setStockQty($stockQty)
    {
        $this->stockQty = $stockQty;
    }

    /**
     *
     * @param mixed $stockValue
     */
    public function setStockValue($stockValue)
    {
        $this->stockValue = $stockValue;
    }
}
