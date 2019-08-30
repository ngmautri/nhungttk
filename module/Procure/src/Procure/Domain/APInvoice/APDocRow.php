<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APDocRowDTOAssembler;

/**
 * AP Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocRow
{

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

    protected $localUnitPrice;

    protected $docUnitPrice;

    protected $exwUnitPrice;

    protected $exwCurrency;

    protected $localNetAmount;

    protected $localGrossAmount;

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionType;

    protected $isDraft;

    protected $isPosted;

    protected $transactionStatus;

    protected $totalExwPrice;

    protected $convertFactorPurchase;

    protected $convertedPurchaseQuantity;

    protected $convertedStockQuantity;

    protected $convertedStockUnitPrice;

    protected $convertedStandardQuantity;

    protected $convertedStandardUnitPrice;

    protected $docQuantity;

    protected $docUnit;

    protected $convertedPurchaseUnitPrice;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $isReversable;

    protected $docType;

    protected $descriptionText;

    protected $vendorItemName;

    protected $reversalBlocked;

    protected $invoice;

    protected $glAccount;

    protected $costCenter;

    protected $docUom;

    protected $prRow;

    protected $createdBy;

    protected $warehouse;

    protected $lastchangeBy;

    protected $poRow;

    protected $item;

    protected $grRow;

   /**
    * 
    * @return NULL|\Procure\Domain\APInvoice\APDocRowSnapshot
    */
    public function makeSnapshot()
    {
        return APDocRowSnapshotAssembler::createSnapshotFrom($this);
    }

  /**
   * 
   * @return NULL|\Procure\Application\DTO\Ap\APDocRowDTO
   */
    public function makeDTO()
    {
        return APDocRowDTOAssembler::createDTOFrom($this);
    }

    /**
     * 
     * @param APDocRowSnapshot $snapshot
     */
    public function makeFromSnapshot(APDocRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof APDocRowSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->rowNumber = $snapshot->rowNumber;
        $this->token = $snapshot->token;
        $this->quantity = $snapshot->quantity;
        $this->unitPrice = $snapshot->unitPrice;
        $this->netAmount = $snapshot->netAmount;
        $this->unit = $snapshot->unit;
        $this->itemUnit = $snapshot->itemUnit;
        $this->conversionFactor = $snapshot->conversionFactor;
        $this->converstionText = $snapshot->converstionText;
        $this->taxRate = $snapshot->taxRate;
        $this->remarks = $snapshot->remarks;
        $this->isActive = $snapshot->isActive;
        $this->createdOn = $snapshot->createdOn;
        $this->lastchangeOn = $snapshot->lastchangeOn;
        $this->currentState = $snapshot->currentState;
        $this->vendorItemCode = $snapshot->vendorItemCode;
        $this->traceStock = $snapshot->traceStock;
        $this->grossAmount = $snapshot->grossAmount;
        $this->taxAmount = $snapshot->taxAmount;
        $this->faRemarks = $snapshot->faRemarks;
        $this->rowIdentifer = $snapshot->rowIdentifer;
        $this->discountRate = $snapshot->discountRate;
        $this->revisionNo = $snapshot->revisionNo;
        $this->localUnitPrice = $snapshot->localUnitPrice;
        $this->docUnitPrice = $snapshot->docUnitPrice;
        $this->exwUnitPrice = $snapshot->exwUnitPrice;
        $this->exwCurrency = $snapshot->exwCurrency;
        $this->localNetAmount = $snapshot->localNetAmount;
        $this->localGrossAmount = $snapshot->localGrossAmount;
        $this->docStatus = $snapshot->docStatus;
        $this->workflowStatus = $snapshot->workflowStatus;
        $this->transactionType = $snapshot->transactionType;
        $this->isDraft = $snapshot->isDraft;
        $this->isPosted = $snapshot->isPosted;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->totalExwPrice = $snapshot->totalExwPrice;
        $this->convertFactorPurchase = $snapshot->convertFactorPurchase;
        $this->convertedPurchaseQuantity = $snapshot->convertedPurchaseQuantity;
        $this->convertedStockQuantity = $snapshot->convertedStockQuantity;
        $this->convertedStockUnitPrice = $snapshot->convertedStockUnitPrice;
        $this->convertedStandardQuantity = $snapshot->convertedStandardQuantity;
        $this->convertedStandardUnitPrice = $snapshot->convertedStandardUnitPrice;
        $this->docQuantity = $snapshot->docQuantity;
        $this->docUnit = $snapshot->docUnit;
        $this->convertedPurchaseUnitPrice = $snapshot->convertedPurchaseUnitPrice;
        $this->isReversed = $snapshot->isReversed;
        $this->reversalDate = $snapshot->reversalDate;
        $this->reversalReason = $snapshot->reversalReason;
        $this->reversalDoc = $snapshot->reversalDoc;
        $this->isReversable = $snapshot->isReversable;
        $this->docType = $snapshot->docType;
        $this->descriptionText = $snapshot->descriptionText;
        $this->vendorItemName = $snapshot->vendorItemName;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->invoice = $snapshot->invoice;
        $this->glAccount = $snapshot->glAccount;
        $this->costCenter = $snapshot->costCenter;
        $this->docUom = $snapshot->docUom;
        $this->prRow = $snapshot->prRow;
        $this->createdBy = $snapshot->createdBy;
        $this->warehouse = $snapshot->warehouse;
        $this->lastchangeBy = $snapshot->lastchangeBy;
        $this->poRow = $snapshot->poRow;
        $this->item = $snapshot->item;
        $this->grRow = $snapshot->grRow;
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
    public function getRowNumber()
    {
        return $this->rowNumber;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
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
    public function getConverstionText()
    {
        return $this->converstionText;
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
    public function getRemarks()
    {
        return $this->remarks;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
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
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     * @return mixed
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @return mixed
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     * @return mixed
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     * @return mixed
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
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
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     * @return mixed
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     * @return mixed
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
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
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
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
    public function getIsDraft()
    {
        return $this->isDraft;
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
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * @return mixed
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     * @return mixed
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
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
    public function getDocQuantity()
    {
        return $this->docQuantity;
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
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
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
    public function getReversalReason()
    {
        return $this->reversalReason;
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
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     * @return mixed
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
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
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
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
    public function getPrRow()
    {
        return $this->prRow;
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
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
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
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    
}
