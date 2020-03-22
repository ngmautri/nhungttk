<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractGR extends AbstractEntity implements AggregateRootInterface
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

    protected $deliveryMode;

    protected $incoterm;

    protected $incotermPlace;

    protected $paymentTerm;

    protected $paymentMethod;

    protected $docStatus;

    protected $isDraft;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $isPosted;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalDoc;

    protected $reversalReason;

    protected $docType;

    protected $reversalBlocked;

    protected $uuid;

    protected $docVersion;

    protected $vendor;

    protected $warehouse;

    protected $createdBy;

    protected $lastchangeBy;

    protected $currency;

    protected $localCurrency;

    protected $docCurrency;

    protected $postingPeriod;

    protected $company;

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
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     *
     * @return mixed
     */
    public function getCurrencyIso3()
    {
        return $this->currencyIso3;
    }

    /**
     *
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
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
    public function getCurrentState()
    {
        return $this->currentState;
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
    public function getTrxType()
    {
        return $this->trxType;
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
    public function getPostingDate()
    {
        return $this->postingDate;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSapDoc()
    {
        return $this->sapDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getContractNo()
    {
        return $this->contractNo;
    }

    /**
     *
     * @return mixed
     */
    public function getContractDate()
    {
        return $this->contractDate;
    }

    /**
     *
     * @return mixed
     */
    public function getQuotationNo()
    {
        return $this->quotationNo;
    }

    /**
     *
     * @return mixed
     */
    public function getQuotationDate()
    {
        return $this->quotationDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
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
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     *
     * @return mixed
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     *
     * @return mixed
     */
    public function getIncotermPlace()
    {
        return $this->incotermPlace;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
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
    public function getIsDraft()
    {
        return $this->isDraft;
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
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
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
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
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
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     *
     * @return mixed
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
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
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $vendorName
     */
    protected function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     *
     * @param mixed $invoiceNo
     */
    protected function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;
    }

    /**
     *
     * @param mixed $invoiceDate
     */
    protected function setInvoiceDate($invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
    }

    /**
     *
     * @param mixed $currencyIso3
     */
    protected function setCurrencyIso3($currencyIso3)
    {
        $this->currencyIso3 = $currencyIso3;
    }

    /**
     *
     * @param mixed $exchangeRate
     */
    protected function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $currentState
     */
    protected function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $trxType
     */
    protected function setTrxType($trxType)
    {
        $this->trxType = $trxType;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    protected function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @param mixed $postingDate
     */
    protected function setPostingDate($postingDate)
    {
        $this->postingDate = $postingDate;
    }

    /**
     *
     * @param mixed $grDate
     */
    protected function setGrDate($grDate)
    {
        $this->grDate = $grDate;
    }

    /**
     *
     * @param mixed $sapDoc
     */
    protected function setSapDoc($sapDoc)
    {
        $this->sapDoc = $sapDoc;
    }

    /**
     *
     * @param mixed $contractNo
     */
    protected function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;
    }

    /**
     *
     * @param mixed $contractDate
     */
    protected function setContractDate($contractDate)
    {
        $this->contractDate = $contractDate;
    }

    /**
     *
     * @param mixed $quotationNo
     */
    protected function setQuotationNo($quotationNo)
    {
        $this->quotationNo = $quotationNo;
    }

    /**
     *
     * @param mixed $quotationDate
     */
    protected function setQuotationDate($quotationDate)
    {
        $this->quotationDate = $quotationDate;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $deliveryMode
     */
    protected function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     *
     * @param mixed $incoterm
     */
    protected function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;
    }

    /**
     *
     * @param mixed $incotermPlace
     */
    protected function setIncotermPlace($incotermPlace)
    {
        $this->incotermPlace = $incotermPlace;
    }

    /**
     *
     * @param mixed $paymentTerm
     */
    protected function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;
    }

    /**
     *
     * @param mixed $paymentMethod
     */
    protected function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     *
     * @param mixed $docStatus
     */
    protected function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     *
     * @param mixed $isDraft
     */
    protected function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     *
     * @param mixed $workflowStatus
     */
    protected function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     *
     * @param mixed $transactionStatus
     */
    protected function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     *
     * @param mixed $isPosted
     */
    protected function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     *
     * @param mixed $isReversed
     */
    protected function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    protected function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @param mixed $docType
     */
    protected function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @param mixed $reversalBlocked
     */
    protected function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $docVersion
     */
    protected function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;
    }

    /**
     *
     * @param mixed $vendor
     */
    protected function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     *
     * @param mixed $warehouse
     */
    protected function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    protected function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @param mixed $currency
     */
    protected function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     *
     * @param mixed $localCurrency
     */
    protected function setLocalCurrency($localCurrency)
    {
        $this->localCurrency = $localCurrency;
    }

    /**
     *
     * @param mixed $docCurrency
     */
    protected function setDocCurrency($docCurrency)
    {
        $this->docCurrency = $docCurrency;
    }

    /**
     *
     * @param mixed $postingPeriod
     */
    protected function setPostingPeriod($postingPeriod)
    {
        $this->postingPeriod = $postingPeriod;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
    }



}