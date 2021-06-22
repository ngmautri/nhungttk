<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractEntity;

/**
 * Abstract Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractRow extends AbstractEntity
{

    // Row Original
    // ====================
    protected $id;

    protected $rowNumber;

    protected $token;

    protected $quantity;

    protected $unitPrice;

    protected $netAmount;

    protected $unit;

    protected $itemUnit;

    protected $conversionFactor;

    protected $converstionText;

    protected $taxRate;

    protected $remarks;

    protected $isActive;

    protected $createdOn;

    protected $lastchangeOn;

    protected $currentState;

    protected $vendorItemCode;

    protected $traceStock;

    protected $grossAmount;

    protected $taxAmount;

    protected $faRemarks;

    protected $rowIdentifer;

    protected $discountRate;

    protected $revisionNo;

    protected $targetObject;

    protected $sourceObject;

    protected $targetObjectId;

    protected $sourceObjectId;

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $isPosted;

    protected $isDraft;

    protected $exwUnitPrice;

    protected $totalExwPrice;

    protected $convertFactorPurchase;

    protected $convertedPurchaseQuantity;

    protected $convertedStandardQuantity;

    protected $convertedStockQuantity;

    protected $convertedStandardUnitPrice;

    protected $convertedStockUnitPrice;

    protected $docQuantity;

    protected $docUnit;

    protected $docUnitPrice;

    protected $convertedPurchaseUnitPrice;

    protected $docType;

    protected $descriptionText;

    protected $vendorItemName;

    protected $reversalBlocked;

    protected $invoice;

    protected $lastchangeBy;

    protected $prRow;

    protected $createdBy;

    protected $warehouse;

    protected $po;

    protected $item;

    protected $docUom;

    protected $docVersion;

    protected $uuid;

    protected $localUnitPrice;

    protected $exwCurrency;

    protected $localNetAmount;

    protected $localGrossAmount;

    protected $transactionType;

    protected $isReversed;

    protected $reversalDate;

    protected $glAccount;

    protected $costCenter;

    protected $standardConvertFactor;

    protected $clearingDocId;

    protected $brand;

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $rowNumber
     */
    protected function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $quantity
     */
    protected function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     *
     * @param mixed $unitPrice
     */
    protected function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     *
     * @param mixed $netAmount
     */
    protected function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @param mixed $unit
     */
    protected function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @param mixed $itemUnit
     */
    protected function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;
    }

    /**
     *
     * @param mixed $conversionFactor
     */
    protected function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     *
     * @param mixed $converstionText
     */
    protected function setConverstionText($converstionText)
    {
        $this->converstionText = $converstionText;
    }

    /**
     *
     * @param mixed $taxRate
     */
    protected function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    protected function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @param mixed $currentState
     */
    protected function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     *
     * @param mixed $vendorItemCode
     */
    protected function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;
    }

    /**
     *
     * @param mixed $traceStock
     */
    protected function setTraceStock($traceStock)
    {
        $this->traceStock = $traceStock;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    protected function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    protected function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @param mixed $faRemarks
     */
    protected function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;
    }

    /**
     *
     * @param mixed $rowIdentifer
     */
    protected function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     *
     * @param mixed $discountRate
     */
    protected function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $targetObject
     */
    protected function setTargetObject($targetObject)
    {
        $this->targetObject = $targetObject;
    }

    /**
     *
     * @param mixed $sourceObject
     */
    protected function setSourceObject($sourceObject)
    {
        $this->sourceObject = $sourceObject;
    }

    /**
     *
     * @param mixed $targetObjectId
     */
    protected function setTargetObjectId($targetObjectId)
    {
        $this->targetObjectId = $targetObjectId;
    }

    /**
     *
     * @param mixed $sourceObjectId
     */
    protected function setSourceObjectId($sourceObjectId)
    {
        $this->sourceObjectId = $sourceObjectId;
    }

    /**
     *
     * @param mixed $docStatus
     */
    protected function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     *
     * @param mixed $workflowStatus
     */
    protected function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     *
     * @param mixed $transactionStatus
     */
    protected function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     *
     * @param mixed $isPosted
     */
    protected function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     *
     * @param mixed $isDraft
     */
    protected function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     *
     * @param mixed $exwUnitPrice
     */
    protected function setExwUnitPrice($exwUnitPrice)
    {
        $this->exwUnitPrice = $exwUnitPrice;
    }

    /**
     *
     * @param mixed $totalExwPrice
     */
    protected function setTotalExwPrice($totalExwPrice)
    {
        $this->totalExwPrice = $totalExwPrice;
    }

    /**
     *
     * @param mixed $convertFactorPurchase
     */
    protected function setConvertFactorPurchase($convertFactorPurchase)
    {
        $this->convertFactorPurchase = $convertFactorPurchase;
    }

    /**
     *
     * @param mixed $convertedPurchaseQuantity
     */
    protected function setConvertedPurchaseQuantity($convertedPurchaseQuantity)
    {
        $this->convertedPurchaseQuantity = $convertedPurchaseQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardQuantity
     */
    protected function setConvertedStandardQuantity($convertedStandardQuantity)
    {
        $this->convertedStandardQuantity = $convertedStandardQuantity;
    }

    /**
     *
     * @param mixed $convertedStockQuantity
     */
    protected function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;
    }

    /**
     *
     * @param mixed $convertedStandardUnitPrice
     */
    protected function setConvertedStandardUnitPrice($convertedStandardUnitPrice)
    {
        $this->convertedStandardUnitPrice = $convertedStandardUnitPrice;
    }

    /**
     *
     * @param mixed $convertedStockUnitPrice
     */
    protected function setConvertedStockUnitPrice($convertedStockUnitPrice)
    {
        $this->convertedStockUnitPrice = $convertedStockUnitPrice;
    }

    /**
     *
     * @param mixed $docQuantity
     */
    protected function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;
    }

    /**
     *
     * @param mixed $docUnit
     */
    protected function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;
    }

    /**
     *
     * @param mixed $docUnitPrice
     */
    protected function setDocUnitPrice($docUnitPrice)
    {
        $this->docUnitPrice = $docUnitPrice;
    }

    /**
     *
     * @param mixed $convertedPurchaseUnitPrice
     */
    protected function setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice)
    {
        $this->convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice;
    }

    /**
     *
     * @param mixed $docType
     */
    protected function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @param mixed $descriptionText
     */
    protected function setDescriptionText($descriptionText)
    {
        $this->descriptionText = $descriptionText;
    }

    /**
     *
     * @param mixed $vendorItemName
     */
    protected function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;
    }

    /**
     *
     * @param mixed $reversalBlocked
     */
    protected function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     *
     * @param mixed $invoice
     */
    protected function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    protected function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @param mixed $prRow
     */
    protected function setPrRow($prRow)
    {
        $this->prRow = $prRow;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $warehouse
     */
    protected function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @param mixed $po
     */
    protected function setPo($po)
    {
        $this->po = $po;
    }

    /**
     *
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @param mixed $docUom
     */
    protected function setDocUom($docUom)
    {
        $this->docUom = $docUom;
    }

    /**
     *
     * @param mixed $docVersion
     */
    protected function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $localUnitPrice
     */
    protected function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;
    }

    /**
     *
     * @param mixed $exwCurrency
     */
    protected function setExwCurrency($exwCurrency)
    {
        $this->exwCurrency = $exwCurrency;
    }

    /**
     *
     * @param mixed $localNetAmount
     */
    protected function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;
    }

    /**
     *
     * @param mixed $localGrossAmount
     */
    protected function setLocalGrossAmount($localGrossAmount)
    {
        $this->localGrossAmount = $localGrossAmount;
    }

    /**
     *
     * @param mixed $transactionType
     */
    protected function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     *
     * @param mixed $isReversed
     */
    protected function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    protected function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @param mixed $glAccount
     */
    protected function setGlAccount($glAccount)
    {
        $this->glAccount = $glAccount;
    }

    /**
     *
     * @param mixed $costCenter
     */
    protected function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    /**
     *
     * @param mixed $standardConvertFactor
     */
    protected function setStandardConvertFactor($standardConvertFactor)
    {
        $this->standardConvertFactor = $standardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     *
     * @return mixed
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     *
     * @return mixed
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     *
     * @return mixed
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     *
     * @return mixed
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     *
     * @return mixed
     */
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @return mixed
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     *
     * @return mixed
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     *
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardConvertFactor()
    {
        return $this->standardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getClearingDocId()
    {
        return $this->clearingDocId;
    }

    /**
     *
     * @param mixed $clearingDocId
     */
    protected function setClearingDocId($clearingDocId)
    {
        $this->clearingDocId = $clearingDocId;
    }

    /**
     *
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     *
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }
}
