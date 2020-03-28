<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APDocRowDTOAssembler;
use Procure\Domain\AbstractRow;

/**
 * AP Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocRow extends AbstractRow
{

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
}
