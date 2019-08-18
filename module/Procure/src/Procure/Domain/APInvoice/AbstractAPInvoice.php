<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APInvoiceDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAPInvoice
{

    protected $id;

    protected $token;

    protected $vendorName;

    protected $invoiceNo;

    protected $invoiceDate;

    protected $currencyIso3;

    protected $exchangeRate;

    protected $remarks;

    protected $createdOn;

    protected $currentState;

    protected $isActive;

    protected $trxType;

    protected $lastchangeOn;

    protected $postingDate;

    protected $grDate;

    protected $sapDoc;

    protected $contractNo;

    protected $contractDate;

    protected $quotationNo;

    protected $quotationDate;

    protected $sysNumber;

    protected $revisionNo;

    protected $currentStatus;

    protected $docStatus;

    protected $workflowStatus;

    protected $paymentTerm;

    protected $transactionType;

    protected $isDraft;

    protected $isPosted;

    protected $transactionStatus;

    protected $incoterm;

    protected $incotermPlace;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalDoc;

    protected $reversalReason;

    protected $paymentStatus;

    protected $isReversable;

    protected $docType;

    protected $totalDocValue;

    protected $totalDocTax;

    protected $totalDocDiscount;

    protected $totalLocalValue;

    protected $totalLocalTax;

    protected $totalLocalDiscount;

    protected $reversalBlocked;

    protected $uuid;

    protected $vendor;

    protected $procureGr;

    protected $localCurrency;

    protected $docCurrency;

    protected $postingPeriod;

    protected $incoterm2;

    protected $pmtTerm;

    protected $warehouse;

    protected $createdBy;

    protected $lastchangeBy;

    protected $currency;

    protected $po;

    protected $company;

    protected $paymentMethod;

    protected $inventoryGr;

    /**
     * 
     * @return NULL|\Procure\Domain\APInvoice\APInvoiceSnapshot
     */
    public function makeSnapshot()
    {
        return APInvoiceSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     * 
     * @return NULL|\Procure\Application\DTO\Ap\APInvoiceDTO
     */
    public function makeDTO()
    {
        return APInvoiceDTOAssembler::createDTOFrom($this);
    }

   /**
    * 
    * @param APInvoiceSnapshot $snapshot
    */
    public function makeFromSnapshot(APInvoiceSnapshot $snapshot)
    {
        if (! $snapshot instanceof APInvoiceSnapshot)
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
    }
}