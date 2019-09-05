<?php
namespace Procure\Domain\PurchaseOrder;

use Procure\Application\DTO\Po\PORowDTOAssembler;
use Procure\Application\DTO\Po\PORowForGridDTO;
use Procure\Application\DTO\DTOFactory;
use Procure\Domain\SnapshotAssembler;
use Procure\Application\DTO\Po\PORowDetailsDTO;

/**
 * AP Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORow
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

    // +++++++++++++++++++ ADTIONAL +++++++++++++++++++++
    protected $draftGrQuantity;

    protected $postedGrQuantity;

    protected $confirmedGrBalance;

    protected $openGrBalance;

    protected $draftAPQuantity;

    protected $postedAPQuantity;

    protected $openAPQuantity;

    protected $billedAmount;

    protected $pr;

    protected $prToken;

    protected $prChecksum;

    protected $prNumber;

    protected $prSysNumber;

    protected $prRowIndentifer;

    protected $prRowCode;

    protected $prRowName;

    protected $prRowConvertFactor;

    protected $prRowUnit;

    protected $prRowVersion;

    protected $itemToken;

    protected $itemCheckSum;

    protected $itemName;

    protected $itemName1;

    protected $itemSKU;

    protected $itemSKU1;

    protected $itemSKU2;

    protected $itemUUID;

    protected $itemSysNumber;

    protected $itemStandardUnit;

    protected $itemStandardUnitName;

    protected $itemVersion;

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseRequest\PRrowSnapshot
     */
    public function makeSnapshot()
    {
        return PORowSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDTO()
    {
        return PORowDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDetailsDTO()
    {
        $dto = new PORowDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDTOForGrid()
    {
        $dto = new PORowForGridDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     */
    public function makeFromSnapshot(PORowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowSnapshot)
            return;

        SnapshotAssembler::makeFromSnapshot($this, $snapshot);
    }

    /**
     *
     * @param PORowDetailsSnapshot $snapshot
     */
    public function makeFromDetailsSnapshot(PORowDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowDetailsSnapshot)
            return;

        SnapshotAssembler::makeFromSnapshot($this, $snapshot);
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
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     *
     * @return mixed
     */
    public function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     *
     * @return mixed
     */
    public function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     *
     * @return mixed
     */
    public function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getPrRowVersion()
    {
        return $this->prRowVersion;
    }
}