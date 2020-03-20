<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Po\PoDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractPO extends AbstractEntity implements AggregateRootInterface
{

    // +++++++++++++++++++ ADTIONAL +++++++++++++++++++++
    protected $paymentTermName;

    protected $paymentTermCode;

    protected $warehouseName;

    protected $warehouseCode;

    protected $paymentMethodName;

    protected $paymentMethodCode;

    protected $incotermCode;

    protected $incotermName;

    protected $createdByName;

    protected $lastChangedByName;

    protected $totalRows;

    protected $totalActiveRows;

    protected $maxRowNumber;

    protected $netAmount;

    protected $taxAmount;

    protected $grossAmount;

    protected $discountAmount;

    protected $billedAmount;

    protected $completedRows;

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

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $docType;

    protected $paymentStatus;

    protected $totalDocValue;

    protected $totalDocTax;

    protected $totalDocDiscount;

    protected $totalLocalValue;

    protected $totalLocalTax;

    protected $totalLocalDiscount;

    protected $reversalBlocked;

    protected $uuid;

    protected $vendor;

    protected $pmtTerm;

    protected $company;

    protected $warehouse;

    protected $createdBy;

    protected $lastchangeBy;

    protected $currency;

    protected $paymentMethod;

    protected $localCurrency;

    protected $docCurrency;

    protected $openAPAmount;

    private function __construct()
    {}

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public function createSnapshot($snapShot)
    {
        if ($snapShot == null) {
            return;
        }

        return SnapshotAssembler::createSnapshotFrom($this, $snapShot);
    }

    /**
     *
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new POSnapshot());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PoDTO
     */
    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new PoDTO());
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentTermName()
    {
        return $this->paymentTermName;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentTermCode()
    {
        return $this->paymentTermCode;
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
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     *
     * @return mixed
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     *
     * @return mixed
     */
    public function getIncotermCode()
    {
        return $this->incotermCode;
    }

    /**
     *
     * @return mixed
     */
    public function getIncotermName()
    {
        return $this->incotermName;
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
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getCompletedRows()
    {
        return $this->completedRows;
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
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }

    /**
     *
     * @param mixed $paymentTermName
     */
    protected function setPaymentTermName($paymentTermName)
    {
        $this->paymentTermName = $paymentTermName;
    }

    /**
     *
     * @param mixed $paymentTermCode
     */
    protected function setPaymentTermCode($paymentTermCode)
    {
        $this->paymentTermCode = $paymentTermCode;
    }

    /**
     *
     * @param mixed $warehouseName
     */
    protected function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

    /**
     *
     * @param mixed $warehouseCode
     */
    protected function setWarehouseCode($warehouseCode)
    {
        $this->warehouseCode = $warehouseCode;
    }

    /**
     *
     * @param mixed $paymentMethodName
     */
    protected function setPaymentMethodName($paymentMethodName)
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    /**
     *
     * @param mixed $paymentMethodCode
     */
    protected function setPaymentMethodCode($paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    /**
     *
     * @param mixed $incotermCode
     */
    protected function setIncotermCode($incotermCode)
    {
        $this->incotermCode = $incotermCode;
    }

    /**
     *
     * @param mixed $incotermName
     */
    protected function setIncotermName($incotermName)
    {
        $this->incotermName = $incotermName;
    }

    /**
     *
     * @param mixed $createdByName
     */
    protected function setCreatedByName($createdByName)
    {
        $this->createdByName = $createdByName;
    }

    /**
     *
     * @param mixed $lastChangedByName
     */
    protected function setLastChangedByName($lastChangedByName)
    {
        $this->lastChangedByName = $lastChangedByName;
    }

    /**
     *
     * @param mixed $totalRows
     */
    protected function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
    }

    /**
     *
     * @param mixed $totalActiveRows
     */
    protected function setTotalActiveRows($totalActiveRows)
    {
        $this->totalActiveRows = $totalActiveRows;
    }

    /**
     *
     * @param mixed $maxRowNumber
     */
    protected function setMaxRowNumber($maxRowNumber)
    {
        $this->maxRowNumber = $maxRowNumber;
    }

    /**
     *
     * @param mixed $netAmount
     */
    protected function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;
    }

    /**
     *
     * @param mixed $taxAmount
     */
    protected function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }

    /**
     *
     * @param mixed $grossAmount
     */
    protected function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;
    }

    /**
     *
     * @param mixed $discountAmount
     */
    protected function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     *
     * @param mixed $billedAmount
     */
    protected function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     *
     * @param mixed $completedRows
     */
    protected function setCompletedRows($completedRows)
    {
        $this->completedRows = $completedRows;
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
     * @param mixed $docStatus
     */
    protected function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
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
     * @param mixed $docType
     */
    protected function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     *
     * @param mixed $paymentStatus
     */
    protected function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     *
     * @param mixed $totalDocValue
     */
    protected function setTotalDocValue($totalDocValue)
    {
        $this->totalDocValue = $totalDocValue;
    }

    /**
     *
     * @param mixed $totalDocTax
     */
    protected function setTotalDocTax($totalDocTax)
    {
        $this->totalDocTax = $totalDocTax;
    }

    /**
     *
     * @param mixed $totalDocDiscount
     */
    protected function setTotalDocDiscount($totalDocDiscount)
    {
        $this->totalDocDiscount = $totalDocDiscount;
    }

    /**
     *
     * @param mixed $totalLocalValue
     */
    protected function setTotalLocalValue($totalLocalValue)
    {
        $this->totalLocalValue = $totalLocalValue;
    }

    /**
     *
     * @param mixed $totalLocalTax
     */
    protected function setTotalLocalTax($totalLocalTax)
    {
        $this->totalLocalTax = $totalLocalTax;
    }

    /**
     *
     * @param mixed $totalLocalDiscount
     */
    protected function setTotalLocalDiscount($totalLocalDiscount)
    {
        $this->totalLocalDiscount = $totalLocalDiscount;
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
     * @param mixed $vendor
     */
    protected function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     *
     * @param mixed $pmtTerm
     */
    protected function setPmtTerm($pmtTerm)
    {
        $this->pmtTerm = $pmtTerm;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
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
     * @param mixed $paymentMethod
     */
    protected function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
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
     * @param mixed $openAPAmount
     */
    protected function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }

}