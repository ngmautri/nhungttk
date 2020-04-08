<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractDTO;

/**
 * Doc Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DocSnapshot extends AbstractDTO
{

    public $docRows;

    public $rowIdArray;

    public $rowsOutput;

    public $postingYear;

    public $postingMonth;

    public $docYear;

    public $docMonth;

    public $companyName;

    public $companyId;

    public $companyToken;

    public $companyCode;

    public $vendorId;

    public $vendorToken;

    public $vendorAddress;

    public $vendorCountry;

    public $paymentTermName;

    public $paymentTermCode;

    public $warehouseName;

    public $warehouseCode;

    public $paymentMethodName;

    public $paymentMethodCode;

    public $incotermCode;

    public $incotermName;

    public $createdByName;

    public $lastChangedByName;

    public $totalRows;

    public $totalActiveRows;

    public $maxRowNumber;

    public $netAmount;

    public $taxAmount;

    public $grossAmount;

    public $discountAmount;

    public $billedAmount;

    public $completedRows;

    public $openAPAmount;

    public $docCurrencyISO;

    public $localCurrencyISO;

    public $docCurrencyId;

    public $localCurrencyId;

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

    public $docVersion;

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

    public $isDraft;

    public $isPosted;

    public $isReversed;

    public $reversalDate;

    public $reversalReason;

    public $postingPeriod;

    public $currentStatus;

    public $transactionType;

    public $discountRate;
    /**
     * @return mixed
     */
    public function getDocRows()
    {
        return $this->docRows;
    }

    /**
     * @return mixed
     */
    public function getRowIdArray()
    {
        return $this->rowIdArray;
    }

    /**
     * @return mixed
     */
    public function getRowsOutput()
    {
        return $this->rowsOutput;
    }

    /**
     * @return mixed
     */
    public function getPostingYear()
    {
        return $this->postingYear;
    }

    /**
     * @return mixed
     */
    public function getPostingMonth()
    {
        return $this->postingMonth;
    }

    /**
     * @return mixed
     */
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return mixed
     */
    public function getCompanyToken()
    {
        return $this->companyToken;
    }

    /**
     * @return mixed
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     * @return mixed
     */
    public function getVendorToken()
    {
        return $this->vendorToken;
    }

    /**
     * @return mixed
     */
    public function getVendorAddress()
    {
        return $this->vendorAddress;
    }

    /**
     * @return mixed
     */
    public function getVendorCountry()
    {
        return $this->vendorCountry;
    }

    /**
     * @return mixed
     */
    public function getPaymentTermName()
    {
        return $this->paymentTermName;
    }

    /**
     * @return mixed
     */
    public function getPaymentTermCode()
    {
        return $this->paymentTermCode;
    }

    /**
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     * @return mixed
     */
    public function getIncotermCode()
    {
        return $this->incotermCode;
    }

    /**
     * @return mixed
     */
    public function getIncotermName()
    {
        return $this->incotermName;
    }

    /**
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     * @return mixed
     */
    public function getLastChangedByName()
    {
        return $this->lastChangedByName;
    }

    /**
     * @return mixed
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * @return mixed
     */
    public function getTotalActiveRows()
    {
        return $this->totalActiveRows;
    }

    /**
     * @return mixed
     */
    public function getMaxRowNumber()
    {
        return $this->maxRowNumber;
    }

    /**
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
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
    public function getCompletedRows()
    {
        return $this->completedRows;
    }

    /**
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }

    /**
     * @return mixed
     */
    public function getDocCurrencyISO()
    {
        return $this->docCurrencyISO;
    }

    /**
     * @return mixed
     */
    public function getLocalCurrencyISO()
    {
        return $this->localCurrencyISO;
    }

    /**
     * @return mixed
     */
    public function getDocCurrencyId()
    {
        return $this->docCurrencyId;
    }

    /**
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
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
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
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
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
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
    public function getDocType()
    {
        return $this->docType;
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
    public function getDocVersion()
    {
        return $this->docVersion;
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
    public function getPmtTerm()
    {
        return $this->pmtTerm;
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
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
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
    public function getIncoterm2()
    {
        return $this->incoterm2;
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
    public function getReversalReason()
    {
        return $this->reversalReason;
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
    public function getCurrentStatus()
    {
        return $this->currentStatus;
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
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

}
