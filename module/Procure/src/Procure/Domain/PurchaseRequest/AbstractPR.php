<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\AggregateRoot;
use Procure\Application\DTO\Pr\PrDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPR extends AggregateRoot
{

    protected $id;

    protected $prAutoNumber;

    protected $prNumber;

    protected $prName;

    protected $keywords;

    protected $remarks;

    protected $createdOn;

    protected $lastChangeOn;

    protected $isDraft;

    protected $isActive;

    protected $status;

    protected $token;

    protected $checksum;

    protected $submittedOn;

    protected $currentState;

    protected $totalRowManual;

    protected $revisionNo;

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $docType;

    protected $reversalBlocked;

    protected $uuid;

    protected $createdBy;

    protected $lastChangeBy;

    protected $department;

    protected $company;

    protected $warehouse;

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseRequest\PRSnapshot
     */
    public function makeSnapshot()
    {
        return PRSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Pr\PrDTO
     */
    public function makeDTO()
    {
        return PrDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param PrSnapshot $snapshot
     */
    public function makeFromSnapshot(PrSnapshot $snapshot)
    {
        if (! $snapshot instanceof PrSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->prAutoNumber = $snapshot->prAutoNumber;
        $this->prNumber = $snapshot->prNumber;
        $this->prName = $snapshot->prName;
        $this->keywords = $snapshot->keywords;
        $this->remarks = $snapshot->remarks;
        $this->createdOn = $snapshot->createdOn;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->isDraft = $snapshot->isDraft;
        $this->isActive = $snapshot->isActive;
        $this->status = $snapshot->status;
        $this->token = $snapshot->token;
        $this->checksum = $snapshot->checksum;
        $this->submittedOn = $snapshot->submittedOn;
        $this->currentState = $snapshot->currentState;
        $this->totalRowManual = $snapshot->totalRowManual;
        $this->revisionNo = $snapshot->revisionNo;
        $this->docStatus = $snapshot->docStatus;
        $this->workflowStatus = $snapshot->workflowStatus;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->docType = $snapshot->docType;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->uuid = $snapshot->uuid;
        $this->createdBy = $snapshot->createdBy;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->department = $snapshot->department;
        $this->company = $snapshot->company;
        $this->warehouse = $snapshot->warehouse;
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
    public function getPrAutoNumber()
    {
        return $this->prAutoNumber;
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
    public function getPrName()
    {
        return $this->prName;
    }

    /**
     *
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
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
    public function getIsDraft()
    {
        return $this->isDraft;
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
    public function getStatus()
    {
        return $this->status;
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
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     *
     * @return mixed
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
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
    public function getTotalRowManual()
    {
        return $this->totalRowManual;
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
    public function getDocType()
    {
        return $this->docType;
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
    public function getUuid()
    {
        return $this->uuid;
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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}