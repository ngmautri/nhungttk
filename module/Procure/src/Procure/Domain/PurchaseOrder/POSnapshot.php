<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POSnapshot extends AbstractDTO
{

    public $id;

    public $token;

    public $vendorName;

    public $invoiceNo;

    public $invoiceDate;

    public $currencyIso3;

    public $exchangeRate;

    public $remarks;

    public $createdOn;

    public $currentState;

    public $isActive;

    public $trxType;

    public $lastchangeOn;

    public $postingDate;

    public $grDate;

    public $sapDoc;

    public $contractNo;

    public $contractDate;

    public $quotationNo;

    public $quotationDate;

    public $sysNumber;

    public $revisionNo;

    public $deliveryMode;

    public $incoterm;

    public $incotermPlace;

    public $paymentTerm;

    public $docStatus;

    public $workflowStatus;

    public $transactionStatus;

    public $docType;

    public $paymentStatus;

    public $totalDocValue;

    public $totalDocTax;

    public $totalDocDiscount;

    public $totalLocalValue;

    public $totalLocalTax;

    public $totalLocalDiscount;

    public $reversalBlocked;

    public $uuid;

    public $vendor;

    public $pmtTerm;

    public $company;

    public $warehouse;

    public $createdBy;

    public $lastchangeBy;

    public $currency;

    public $paymentMethod;

    public $localCurrency;

    public $docCurrency;

    public $incoterm2;

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
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalDocValue()
    {
        return $this->totalDocValue;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalDocTax()
    {
        return $this->totalDocTax;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalDocDiscount()
    {
        return $this->totalDocDiscount;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalLocalValue()
    {
        return $this->totalLocalValue;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalLocalTax()
    {
        return $this->totalLocalTax;
    }

    /**
     *
     * @return mixed
     */
    public function getTotalLocalDiscount()
    {
        return $this->totalLocalDiscount;
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
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     *
     * @return mixed
     */
    public function getPmtTerm()
    {
        return $this->pmtTerm;
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
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
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
    public function getIncoterm2()
    {
        return $this->incoterm2;
    }
}