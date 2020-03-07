<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Po\PoDTOAssembler;
use Application\Domain\Shared\DTOFactory;
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
}