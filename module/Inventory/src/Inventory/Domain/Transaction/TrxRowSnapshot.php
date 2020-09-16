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

    public $stockQty;

    public $stockValue;

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
}