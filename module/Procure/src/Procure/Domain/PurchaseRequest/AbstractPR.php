<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\AggregateRoot;
use Procure\Application\DTO\Pr\PrDTO;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\DTOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPR extends AggregateRoot
{

    protected $warehouseName;

    protected $warehouseCode;

    protected $createdByName;

    protected $lastChangedByName;

    protected $totalRows;

    protected $totalActiveRows;

    protected $maxRowNumber;

    protected $completedRows;

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
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new PRSnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new PrDTO());
    }

    /**
     *
     * @param PrSnapshot $snapshot
     */
    public function makeFromSnapshot(PrSnapshot $snapshot)
    {
        if (! $snapshot instanceof PrSnapshot)
            return;

        SnapshotAssembler::makeFromSnapshot($this, $snapshot);
    }

    /**
     *
     * @param PRDetailsSnapshot $snapshot
     */
    public function makeFromDetailsSnapshot(PRDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRDetailsSnapshot)
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

    /**
     *
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangedByName()
    {
        return $this->lastChangedByName;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalActiveRows()
    {
        return $this->totalActiveRows;
    }

    /**
     *
     * @return mixed
     */
    public function getMaxRowNumber()
    {
        return $this->maxRowNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getCompletedRows()
    {
        return $this->completedRows;
    }
}