<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Application\DTO\Pr\PrRowDTOAssembler;

/**
 * AP Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRow
{
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
    protected $pr;
    protected $item;
    protected $project;
    protected $lastChangeBy;
    protected $docUom;
    protected $warehouse;

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseRequest\PRrowSnapshot
     */
    public function makeSnapshot()
    {
        return PRRowSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Pr\PrRowDTO
     */
    public function makeDTO()
    {
        return PrRowDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param PrRowSnapshot $snapshot
     */
    public function makeFromSnapshot(PrRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PrRowSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->rowNumber = $snapshot->rowNumber;
        $this->rowIdentifer = $snapshot->rowIdentifer;
        $this->token = $snapshot->token;
        $this->checksum = $snapshot->checksum;
        $this->priority = $snapshot->priority;
        $this->rowName = $snapshot->rowName;
        $this->rowDescription = $snapshot->rowDescription;
        $this->rowCode = $snapshot->rowCode;
        $this->rowUnit = $snapshot->rowUnit;
        $this->conversionFactor = $snapshot->conversionFactor;
        $this->conversionText = $snapshot->conversionText;
        $this->quantity = $snapshot->quantity;
        $this->edt = $snapshot->edt;
        $this->isDraft = $snapshot->isDraft;
        $this->isActive = $snapshot->isActive;
        $this->createdOn = $snapshot->createdOn;
        $this->remarks = $snapshot->remarks;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->currentState = $snapshot->currentState;
        $this->faRemarks = $snapshot->faRemarks;
        $this->revisionNo = $snapshot->revisionNo;
        $this->docStatus = $snapshot->docStatus;
        $this->workflowStatus = $snapshot->workflowStatus;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->convertedStockQuantity = $snapshot->convertedStockQuantity;
        $this->convertedStandardQuantiy = $snapshot->convertedStandardQuantiy;
        $this->docQuantity = $snapshot->docQuantity;
        $this->docUnit = $snapshot->docUnit;
        $this->docType = $snapshot->docType;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->createdBy = $snapshot->createdBy;
        $this->pr = $snapshot->pr;
        $this->item = $snapshot->item;
        $this->project = $snapshot->project;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->docUom = $snapshot->docUom;
        $this->warehouse = $snapshot->warehouse;
    }
    
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $rowNumber
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;
    }

    /**
     * @param mixed $rowIdentifer
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @param mixed $rowName
     */
    public function setRowName($rowName)
    {
        $this->rowName = $rowName;
    }

    /**
     * @param mixed $rowDescription
     */
    public function setRowDescription($rowDescription)
    {
        $this->rowDescription = $rowDescription;
    }

    /**
     * @param mixed $rowCode
     */
    public function setRowCode($rowCode)
    {
        $this->rowCode = $rowCode;
    }

    /**
     * @param mixed $rowUnit
     */
    public function setRowUnit($rowUnit)
    {
        $this->rowUnit = $rowUnit;
    }

    /**
     * @param mixed $conversionFactor
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;
    }

    /**
     * @param mixed $conversionText
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param mixed $edt
     */
    public function setEdt($edt)
    {
        $this->edt = $edt;
    }

    /**
     * @param mixed $isDraft
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     * @param mixed $currentState
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     * @param mixed $faRemarks
     */
    public function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;
    }

    /**
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     * @param mixed $docStatus
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     * @param mixed $workflowStatus
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     * @param mixed $transactionStatus
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     * @param mixed $convertedStockQuantity
     */
    public function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;
    }

    /**
     * @param mixed $convertedStandardQuantiy
     */
    public function setConvertedStandardQuantiy($convertedStandardQuantiy)
    {
        $this->convertedStandardQuantiy = $convertedStandardQuantiy;
    }

    /**
     * @param mixed $docQuantity
     */
    public function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;
    }

    /**
     * @param mixed $docUnit
     */
    public function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;
    }

    /**
     * @param mixed $docType
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     * @param mixed $reversalBlocked
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @param mixed $pr
     */
    public function setPr($pr)
    {
        $this->pr = $pr;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     * @param mixed $docUom
     */
    public function setDocUom($docUom)
    {
        $this->docUom = $docUom;
    }

    /**
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

}
