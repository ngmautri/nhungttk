<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APInvoiceDTOAssembler;
use Application\Domain\Shared\AggregateRoot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAPInvoice extends AggregateRoot
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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * @return mixed
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * @return mixed
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * @return mixed
     */
    public function getCurrencyIso3()
    {
        return $this->currencyIso3;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
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
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getTrxType()
    {
        return $this->trxType;
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
    public function getPostingDate()
    {
        return $this->postingDate;
    }

    /**
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     * @return mixed
     */
    public function getSapDoc()
    {
        return $this->sapDoc;
    }

    /**
     * @return mixed
     */
    public function getContractNo()
    {
        return $this->contractNo;
    }

    /**
     * @return mixed
     */
    public function getContractDate()
    {
        return $this->contractDate;
    }

    /**
     * @return mixed
     */
    public function getQuotationNo()
    {
        return $this->quotationNo;
    }

    /**
     * @return mixed
     */
    public function getQuotationDate()
    {
        return $this->quotationDate;
    }

    /**
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
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
    public function getCurrentStatus()
    {
        return $this->currentStatus;
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
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
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
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * @return mixed
     */
    public function getIncotermPlace()
    {
        return $this->incotermPlace;
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
    public function getReversalDoc()
    {
        return $this->reversalDoc;
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
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
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
    public function getTotalDocValue()
    {
        return $this->totalDocValue;
    }

    /**
     * @return mixed
     */
    public function getTotalDocTax()
    {
        return $this->totalDocTax;
    }

    /**
     * @return mixed
     */
    public function getTotalDocDiscount()
    {
        return $this->totalDocDiscount;
    }

    /**
     * @return mixed
     */
    public function getTotalLocalValue()
    {
        return $this->totalLocalValue;
    }

    /**
     * @return mixed
     */
    public function getTotalLocalTax()
    {
        return $this->totalLocalTax;
    }

    /**
     * @return mixed
     */
    public function getTotalLocalDiscount()
    {
        return $this->totalLocalDiscount;
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
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
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
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     * @return mixed
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     * @return mixed
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     * @return mixed
     */
    public function getIncoterm2()
    {
        return $this->incoterm2;
    }

    /**
     * @return mixed
     */
    public function getPmtTerm()
    {
        return $this->pmtTerm;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
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
    public function getCurrency()
    {
        return $this->currency;
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @return mixed
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }

}