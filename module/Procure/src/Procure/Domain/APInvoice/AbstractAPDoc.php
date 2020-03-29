<?php
namespace Procure\Domain\APInvoice;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Ap\APDocDTOAssembler;
use Procure\Domain\AbstractDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAPDoc extends AbstractDoc
{
  
    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new APDocSnapshot());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Ap\APDocDTO
     */
    public function makeDTO()
    {
        return APDocDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param APDocSnapshot $snapshot
     */
    public function makeFromSnapshot(APDocSnapshot $snapshot)
    {
        if (! $snapshot instanceof APDocSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->token = $snapshot->token;
        $this->vendorName = $snapshot->vendorName;
        $this->invoiceNo = $snapshot->invoiceNo;
        $this->invoiceDate = $snapshot->invoiceDate;
        $this->currencyIso3 = $snapshot->currencyIso3;
        $this->exchangeRate = $snapshot->exchangeRate;
        $this->remarks = $snapshot->remarks;
        $this->createdOn = $snapshot->createdOn;
        $this->currentState = $snapshot->currentState;
        $this->isActive = $snapshot->isActive;
        $this->trxType = $snapshot->trxType;
        $this->lastchangeOn = $snapshot->lastchangeOn;
        $this->postingDate = $snapshot->postingDate;
        $this->grDate = $snapshot->grDate;
        $this->sapDoc = $snapshot->sapDoc;
        $this->contractNo = $snapshot->contractNo;
        $this->contractDate = $snapshot->contractDate;
        $this->quotationNo = $snapshot->quotationNo;
        $this->quotationDate = $snapshot->quotationDate;
        $this->sysNumber = $snapshot->sysNumber;
        $this->revisionNo = $snapshot->revisionNo;
        $this->currentStatus = $snapshot->currentStatus;
        $this->docStatus = $snapshot->docStatus;
        $this->workflowStatus = $snapshot->workflowStatus;
        $this->paymentTerm = $snapshot->paymentTerm;
        $this->transactionType = $snapshot->transactionType;
        $this->isDraft = $snapshot->isDraft;
        $this->isPosted = $snapshot->isPosted;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->incoterm = $snapshot->incoterm;
        $this->incotermPlace = $snapshot->incotermPlace;
        $this->isReversed = $snapshot->isReversed;
        $this->reversalDate = $snapshot->reversalDate;
        $this->reversalDoc = $snapshot->reversalDoc;
        $this->reversalReason = $snapshot->reversalReason;
        $this->paymentStatus = $snapshot->paymentStatus;
        $this->isReversable = $snapshot->isReversable;
        $this->docType = $snapshot->docType;
        $this->totalDocValue = $snapshot->totalDocValue;
        $this->totalDocTax = $snapshot->totalDocTax;
        $this->totalDocDiscount = $snapshot->totalDocDiscount;
        $this->totalLocalValue = $snapshot->totalLocalValue;
        $this->totalLocalTax = $snapshot->totalLocalTax;
        $this->totalLocalDiscount = $snapshot->totalLocalDiscount;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->uuid = $snapshot->uuid;
        $this->vendor = $snapshot->vendor;
        $this->procureGr = $snapshot->procureGr;
        $this->localCurrency = $snapshot->localCurrency;
        $this->docCurrency = $snapshot->docCurrency;
        $this->postingPeriod = $snapshot->postingPeriod;
        $this->incoterm2 = $snapshot->incoterm2;
        $this->pmtTerm = $snapshot->pmtTerm;
        $this->warehouse = $snapshot->warehouse;
        $this->createdBy = $snapshot->createdBy;
        $this->lastchangeBy = $snapshot->lastchangeBy;
        $this->currency = $snapshot->currency;
        $this->po = $snapshot->po;
        $this->company = $snapshot->company;
        $this->paymentMethod = $snapshot->paymentMethod;
        $this->inventoryGr = $snapshot->inventoryGr;

        $this->specify();
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
    public function getProcureGr()
    {
        return $this->procureGr;
    }

    /**
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * @return mixed
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }
    /**
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     * @param mixed $isReversable
     */
    protected function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     * @param mixed $procureGr
     */
    protected function setProcureGr($procureGr)
    {
        $this->procureGr = $procureGr;
    }

    /**
     * @param mixed $po
     */
    protected function setPo($po)
    {
        $this->po = $po;
    }

    /**
     * @param mixed $inventoryGr
     */
    protected function setInventoryGr($inventoryGr)
    {
        $this->inventoryGr = $inventoryGr;
    }


}