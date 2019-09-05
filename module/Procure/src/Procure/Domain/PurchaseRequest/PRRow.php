<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Application\DTO\DTOFactory;
use Procure\Domain\SnapshotAssembler;
use Procure\Application\DTO\Pr\PRRowDTO;

/**
 * Document Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRow
{

    protected $prName;
    protected $prYear;
    protected $draftPOQuantity;
    protected $postedPOQuantity;
    protected $draftStockGRQuantity;
    protected $postedStockGRQuantity;
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
    protected $lastVendorName;
    protected $lastUnitPrice;
    protected $lastCurrency;
    protected $id;
    protected $rowNumber;
    protected $rowIdentifer;
    protected $token;
    protected $checksum;
    protected $priority;
    protected $rowName;
    protected $rowDescription;
    protected $rowCode;
    protected $rowUnit;
    protected $conversionFactor;
    protected $conversionText;
    protected $quantity;
    protected $edt;
    protected $isDraft;
    protected $isActive;
    protected $createdOn;
    protected $remarks;
    protected $lastChangeOn;
    protected $currentState;
    protected $faRemarks;
    protected $revisionNo;
    protected $docStatus;
    protected $workflowStatus;
    protected $transactionStatus;
    protected $convertedStockQuantity;
    protected $convertedStandardQuantiy;
    protected $docQuantity;
    protected $docUnit;
    protected $docType;
    protected $reversalBlocked;
    protected $createdBy;
    protected $item;
    protected $project;
    protected $lastChangeBy;
    protected $docUom;
    protected $warehouse;

    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new PRRowSnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new PRRowDTO());
    }

    /**
     *
     * @param PrSnapshot $snapshot
     */
    public function makeFromSnapshot(PrRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PrRowSnapshot)
            return;

        SnapshotAssembler::makeFromSnapshot($this, $snapshot);
    }

    /**
     *
     * @param PRDetailsSnapshot $snapshot
     */
    public function makeFromDetailsSnapshot(PRRowDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRRowDetailsSnapshot)
            return;
            
            SnapshotAssembler::makeFromSnapshot($this, $snapshot);
    }
    /**
     * @return mixed
     */
    public function getPrName()
    {
        return $this->prName;
    }

    /**
     * @return mixed
     */
    public function getPrYear()
    {
        return $this->prYear;
    }

    /**
     * @return mixed
     */
    public function getDraftPOQuantity()
    {
        return $this->draftPOQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedPOQuantity()
    {
        return $this->postedPOQuantity;
    }

    /**
     * @return mixed
     */
    public function getDraftStockGRQuantity()
    {
        return $this->draftStockGRQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedStockGRQuantity()
    {
        return $this->postedStockGRQuantity;
    }

    /**
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
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
    public function getPrToken()
    {
        return $this->prToken;
    }

    /**
     * @return mixed
     */
    public function getPrChecksum()
    {
        return $this->prChecksum;
    }

    /**
     * @return mixed
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     * @return mixed
     */
    public function getPrSysNumber()
    {
        return $this->prSysNumber;
    }

    /**
     * @return mixed
     */
    public function getPrRowIndentifer()
    {
        return $this->prRowIndentifer;
    }

    /**
     * @return mixed
     */
    public function getPrRowCode()
    {
        return $this->prRowCode;
    }

    /**
     * @return mixed
     */
    public function getPrRowName()
    {
        return $this->prRowName;
    }

    /**
     * @return mixed
     */
    public function getPrRowConvertFactor()
    {
        return $this->prRowConvertFactor;
    }

    /**
     * @return mixed
     */
    public function getPrRowUnit()
    {
        return $this->prRowUnit;
    }

    /**
     * @return mixed
     */
    public function getPrRowVersion()
    {
        return $this->prRowVersion;
    }

    /**
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     * @return mixed
     */
    public function getItemCheckSum()
    {
        return $this->itemCheckSum;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @return mixed
     */
    public function getItemName1()
    {
        return $this->itemName1;
    }

    /**
     * @return mixed
     */
    public function getItemSKU()
    {
        return $this->itemSKU;
    }

    /**
     * @return mixed
     */
    public function getItemSKU1()
    {
        return $this->itemSKU1;
    }

    /**
     * @return mixed
     */
    public function getItemSKU2()
    {
        return $this->itemSKU2;
    }

    /**
     * @return mixed
     */
    public function getItemUUID()
    {
        return $this->itemUUID;
    }

    /**
     * @return mixed
     */
    public function getItemSysNumber()
    {
        return $this->itemSysNumber;
    }

    /**
     * @return mixed
     */
    public function getItemStandardUnit()
    {
        return $this->itemStandardUnit;
    }

    /**
     * @return mixed
     */
    public function getItemStandardUnitName()
    {
        return $this->itemStandardUnitName;
    }

    /**
     * @return mixed
     */
    public function getItemVersion()
    {
        return $this->itemVersion;
    }

    /**
     * @return mixed
     */
    public function getLastVendorName()
    {
        return $this->lastVendorName;
    }

    /**
     * @return mixed
     */
    public function getLastUnitPrice()
    {
        return $this->lastUnitPrice;
    }

    /**
     * @return mixed
     */
    public function getLastCurrency()
    {
        return $this->lastCurrency;
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
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
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
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return mixed
     */
    public function getRowName()
    {
        return $this->rowName;
    }

    /**
     * @return mixed
     */
    public function getRowDescription()
    {
        return $this->rowDescription;
    }

    /**
     * @return mixed
     */
    public function getRowCode()
    {
        return $this->rowCode;
    }

    /**
     * @return mixed
     */
    public function getRowUnit()
    {
        return $this->rowUnit;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getEdt()
    {
        return $this->edt;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getFaRemarks()
    {
        return $this->faRemarks;
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
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
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
    public function getConvertedStandardQuantiy()
    {
        return $this->convertedStandardQuantiy;
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
    public function getDocType()
    {
        return $this->docType;
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
    public function getItem()
    {
        return $this->item;
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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
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
    public function getWarehouse()
    {
        return $this->warehouse;
    }

  }
