<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinVendorInvoice
 *
 * @ORM\Table(name="fin_vendor_invoice", indexes={@ORM\Index(name="fin_vendor_invoice_FK1_idx", columns={"vendor_id"}), @ORM\Index(name="fin_vendor_invoice_FK2_idx", columns={"warehouse_id"}), @ORM\Index(name="fin_vendor_invoice_FK3_idx", columns={"created_by"}), @ORM\Index(name="fin_vendor_invoice_FK5_idx", columns={"lastchange_by"}), @ORM\Index(name="fin_vendor_invoice_FK5_idx1", columns={"currency_id"}), @ORM\Index(name="fin_vendor_invoice_FK6_idx", columns={"po_id"}), @ORM\Index(name="fin_vendor_invoice_FK7_idx", columns={"company_id"}), @ORM\Index(name="fin_vendor_invoice_FK8_idx", columns={"payment_method"}), @ORM\Index(name="fin_vendor_invoice_FK9_idx", columns={"inventory_gr_id"}), @ORM\Index(name="fin_vendor_invoice_FK10_idx", columns={"procure_gr_id"}), @ORM\Index(name="fin_vendor_invoice_FK11_idx", columns={"local_currency_id"}), @ORM\Index(name="fin_vendor_invoice_FK12_idx", columns={"doc_currency_id"}), @ORM\Index(name="fin_vendor_invoice_FK13_idx", columns={"posting_period_id"}), @ORM\Index(name="fin_vendor_invoice_FK14_idx", columns={"incoterm_id"}), @ORM\Index(name="fin_vendor_invoice_FK15_idx", columns={"pmt_term_id"})})
 * @ORM\Entity
 */
class FinVendorInvoice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_name", type="string", length=100, nullable=true)
     */
    private $vendorName;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_no", type="string", length=45, nullable=true)
     */
    private $invoiceNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="invoice_date", type="datetime", nullable=true)
     */
    private $invoiceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_iso3", type="string", length=3, nullable=true)
     */
    private $currencyIso3;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="trx_type", type="string", length=45, nullable=true)
     */
    private $trxType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gr_date", type="datetime", nullable=true)
     */
    private $grDate;

    /**
     * @var string
     *
     * @ORM\Column(name="sap_doc", type="string", length=45, nullable=true)
     */
    private $sapDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_no", type="string", length=45, nullable=true)
     */
    private $contractNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contract_date", type="datetime", nullable=true)
     */
    private $contractDate;

    /**
     * @var string
     *
     * @ORM\Column(name="quotation_no", type="string", length=45, nullable=true)
     */
    private $quotationNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="quotation_date", type="datetime", nullable=true)
     */
    private $quotationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="current_status", type="string", length=45, nullable=true)
     */
    private $currentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_term", type="string", length=45, nullable=true)
     */
    private $paymentTerm;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=45, nullable=true)
     */
    private $transactionType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_posted", type="boolean", nullable=true)
     */
    private $isPosted;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm", type="string", length=45, nullable=true)
     */
    private $incoterm;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm_place", type="string", length=45, nullable=true)
     */
    private $incotermPlace;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversed", type="boolean", nullable=true)
     */
    private $isReversed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reversal_date", type="datetime", nullable=true)
     */
    private $reversalDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="reversal_doc", type="integer", nullable=true)
     */
    private $reversalDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="reversal_reason", type="string", length=100, nullable=true)
     */
    private $reversalReason;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_status", type="string", length=10, nullable=true)
     */
    private $paymentStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversable", type="boolean", nullable=true)
     */
    private $isReversable;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var string
     *
     * @ORM\Column(name="total_doc_value", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalDocValue;

    /**
     * @var string
     *
     * @ORM\Column(name="total_doc_tax", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalDocTax;

    /**
     * @var string
     *
     * @ORM\Column(name="total_doc_discount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalDocDiscount;

    /**
     * @var string
     *
     * @ORM\Column(name="total_local_value", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalLocalValue;

    /**
     * @var string
     *
     * @ORM\Column(name="total_local_tax", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalLocalTax;

    /**
     * @var string
     *
     * @ORM\Column(name="total_local_discount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalLocalDiscount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="discount_rate", type="decimal", precision=2, scale=2, nullable=true)
     */
    private $discountRate;

    /**
     * @var string
     *
     * @ORM\Column(name="discount_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $discountAmount;

    /**
     * @var \Application\Entity\NmtBpVendor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtBpVendor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

    /**
     * @var \Application\Entity\NmtProcureGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="procure_gr_id", referencedColumnName="id")
     * })
     */
    private $procureGr;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="local_currency_id", referencedColumnName="id")
     * })
     */
    private $localCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_currency_id", referencedColumnName="id")
     * })
     */
    private $docCurrency;

    /**
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posting_period_id", referencedColumnName="id")
     * })
     */
    private $postingPeriod;

    /**
     * @var \Application\Entity\NmtApplicationIncoterms
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationIncoterms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="incoterm_id", referencedColumnName="id")
     * })
     */
    private $incoterm2;

    /**
     * @var \Application\Entity\NmtApplicationPmtTerm
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtTerm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pmt_term_id", referencedColumnName="id")
     * })
     */
    private $pmtTerm;

    /**
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     * })
     */
    private $warehouse;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    private $currency;

    /**
     * @var \Application\Entity\NmtProcurePo
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     * })
     */
    private $po;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var \Application\Entity\NmtApplicationPmtMethod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_method", referencedColumnName="id")
     * })
     */
    private $paymentMethod;

    /**
     * @var \Application\Entity\NmtInventoryGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_gr_id", referencedColumnName="id")
     * })
     */
    private $inventoryGr;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return FinVendorInvoice
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set vendorName
     *
     * @param string $vendorName
     *
     * @return FinVendorInvoice
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;

        return $this;
    }

    /**
     * Get vendorName
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * Set invoiceNo
     *
     * @param string $invoiceNo
     *
     * @return FinVendorInvoice
     */
    public function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;

        return $this;
    }

    /**
     * Get invoiceNo
     *
     * @return string
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * Set invoiceDate
     *
     * @param \DateTime $invoiceDate
     *
     * @return FinVendorInvoice
     */
    public function setInvoiceDate($invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    /**
     * Get invoiceDate
     *
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * Set currencyIso3
     *
     * @param string $currencyIso3
     *
     * @return FinVendorInvoice
     */
    public function setCurrencyIso3($currencyIso3)
    {
        $this->currencyIso3 = $currencyIso3;

        return $this;
    }

    /**
     * Get currencyIso3
     *
     * @return string
     */
    public function getCurrencyIso3()
    {
        return $this->currencyIso3;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return FinVendorInvoice
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Get exchangeRate
     *
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return FinVendorInvoice
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinVendorInvoice
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return FinVendorInvoice
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return FinVendorInvoice
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set trxType
     *
     * @param string $trxType
     *
     * @return FinVendorInvoice
     */
    public function setTrxType($trxType)
    {
        $this->trxType = $trxType;

        return $this;
    }

    /**
     * Get trxType
     *
     * @return string
     */
    public function getTrxType()
    {
        return $this->trxType;
    }

    /**
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return FinVendorInvoice
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return FinVendorInvoice
     */
    public function setPostingDate($postingDate)
    {
        $this->postingDate = $postingDate;

        return $this;
    }

    /**
     * Get postingDate
     *
     * @return \DateTime
     */
    public function getPostingDate()
    {
        return $this->postingDate;
    }

    /**
     * Set grDate
     *
     * @param \DateTime $grDate
     *
     * @return FinVendorInvoice
     */
    public function setGrDate($grDate)
    {
        $this->grDate = $grDate;

        return $this;
    }

    /**
     * Get grDate
     *
     * @return \DateTime
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     * Set sapDoc
     *
     * @param string $sapDoc
     *
     * @return FinVendorInvoice
     */
    public function setSapDoc($sapDoc)
    {
        $this->sapDoc = $sapDoc;

        return $this;
    }

    /**
     * Get sapDoc
     *
     * @return string
     */
    public function getSapDoc()
    {
        return $this->sapDoc;
    }

    /**
     * Set contractNo
     *
     * @param string $contractNo
     *
     * @return FinVendorInvoice
     */
    public function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;

        return $this;
    }

    /**
     * Get contractNo
     *
     * @return string
     */
    public function getContractNo()
    {
        return $this->contractNo;
    }

    /**
     * Set contractDate
     *
     * @param \DateTime $contractDate
     *
     * @return FinVendorInvoice
     */
    public function setContractDate($contractDate)
    {
        $this->contractDate = $contractDate;

        return $this;
    }

    /**
     * Get contractDate
     *
     * @return \DateTime
     */
    public function getContractDate()
    {
        return $this->contractDate;
    }

    /**
     * Set quotationNo
     *
     * @param string $quotationNo
     *
     * @return FinVendorInvoice
     */
    public function setQuotationNo($quotationNo)
    {
        $this->quotationNo = $quotationNo;

        return $this;
    }

    /**
     * Get quotationNo
     *
     * @return string
     */
    public function getQuotationNo()
    {
        return $this->quotationNo;
    }

    /**
     * Set quotationDate
     *
     * @param \DateTime $quotationDate
     *
     * @return FinVendorInvoice
     */
    public function setQuotationDate($quotationDate)
    {
        $this->quotationDate = $quotationDate;

        return $this;
    }

    /**
     * Get quotationDate
     *
     * @return \DateTime
     */
    public function getQuotationDate()
    {
        return $this->quotationDate;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return FinVendorInvoice
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;

        return $this;
    }

    /**
     * Get sysNumber
     *
     * @return string
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return FinVendorInvoice
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set currentStatus
     *
     * @param string $currentStatus
     *
     * @return FinVendorInvoice
     */
    public function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;

        return $this;
    }

    /**
     * Get currentStatus
     *
     * @return string
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return FinVendorInvoice
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;

        return $this;
    }

    /**
     * Get docStatus
     *
     * @return string
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return FinVendorInvoice
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;

        return $this;
    }

    /**
     * Get workflowStatus
     *
     * @return string
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * Set paymentTerm
     *
     * @param string $paymentTerm
     *
     * @return FinVendorInvoice
     */
    public function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * Get paymentTerm
     *
     * @return string
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return FinVendorInvoice
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return FinVendorInvoice
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isPosted
     *
     * @param boolean $isPosted
     *
     * @return FinVendorInvoice
     */
    public function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;

        return $this;
    }

    /**
     * Get isPosted
     *
     * @return boolean
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return FinVendorInvoice
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * Get transactionStatus
     *
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Set incoterm
     *
     * @param string $incoterm
     *
     * @return FinVendorInvoice
     */
    public function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;

        return $this;
    }

    /**
     * Get incoterm
     *
     * @return string
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * Set incotermPlace
     *
     * @param string $incotermPlace
     *
     * @return FinVendorInvoice
     */
    public function setIncotermPlace($incotermPlace)
    {
        $this->incotermPlace = $incotermPlace;

        return $this;
    }

    /**
     * Get incotermPlace
     *
     * @return string
     */
    public function getIncotermPlace()
    {
        return $this->incotermPlace;
    }

    /**
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return FinVendorInvoice
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    /**
     * Get isReversed
     *
     * @return boolean
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * Set reversalDate
     *
     * @param \DateTime $reversalDate
     *
     * @return FinVendorInvoice
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;

        return $this;
    }

    /**
     * Get reversalDate
     *
     * @return \DateTime
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * Set reversalDoc
     *
     * @param integer $reversalDoc
     *
     * @return FinVendorInvoice
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;

        return $this;
    }

    /**
     * Get reversalDoc
     *
     * @return integer
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     * Set reversalReason
     *
     * @param string $reversalReason
     *
     * @return FinVendorInvoice
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;

        return $this;
    }

    /**
     * Get reversalReason
     *
     * @return string
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * Set paymentStatus
     *
     * @param string $paymentStatus
     *
     * @return FinVendorInvoice
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return string
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set isReversable
     *
     * @param boolean $isReversable
     *
     * @return FinVendorInvoice
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;

        return $this;
    }

    /**
     * Get isReversable
     *
     * @return boolean
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     * Set docType
     *
     * @param string $docType
     *
     * @return FinVendorInvoice
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;

        return $this;
    }

    /**
     * Get docType
     *
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * Set totalDocValue
     *
     * @param string $totalDocValue
     *
     * @return FinVendorInvoice
     */
    public function setTotalDocValue($totalDocValue)
    {
        $this->totalDocValue = $totalDocValue;

        return $this;
    }

    /**
     * Get totalDocValue
     *
     * @return string
     */
    public function getTotalDocValue()
    {
        return $this->totalDocValue;
    }

    /**
     * Set totalDocTax
     *
     * @param string $totalDocTax
     *
     * @return FinVendorInvoice
     */
    public function setTotalDocTax($totalDocTax)
    {
        $this->totalDocTax = $totalDocTax;

        return $this;
    }

    /**
     * Get totalDocTax
     *
     * @return string
     */
    public function getTotalDocTax()
    {
        return $this->totalDocTax;
    }

    /**
     * Set totalDocDiscount
     *
     * @param string $totalDocDiscount
     *
     * @return FinVendorInvoice
     */
    public function setTotalDocDiscount($totalDocDiscount)
    {
        $this->totalDocDiscount = $totalDocDiscount;

        return $this;
    }

    /**
     * Get totalDocDiscount
     *
     * @return string
     */
    public function getTotalDocDiscount()
    {
        return $this->totalDocDiscount;
    }

    /**
     * Set totalLocalValue
     *
     * @param string $totalLocalValue
     *
     * @return FinVendorInvoice
     */
    public function setTotalLocalValue($totalLocalValue)
    {
        $this->totalLocalValue = $totalLocalValue;

        return $this;
    }

    /**
     * Get totalLocalValue
     *
     * @return string
     */
    public function getTotalLocalValue()
    {
        return $this->totalLocalValue;
    }

    /**
     * Set totalLocalTax
     *
     * @param string $totalLocalTax
     *
     * @return FinVendorInvoice
     */
    public function setTotalLocalTax($totalLocalTax)
    {
        $this->totalLocalTax = $totalLocalTax;

        return $this;
    }

    /**
     * Get totalLocalTax
     *
     * @return string
     */
    public function getTotalLocalTax()
    {
        return $this->totalLocalTax;
    }

    /**
     * Set totalLocalDiscount
     *
     * @param string $totalLocalDiscount
     *
     * @return FinVendorInvoice
     */
    public function setTotalLocalDiscount($totalLocalDiscount)
    {
        $this->totalLocalDiscount = $totalLocalDiscount;

        return $this;
    }

    /**
     * Get totalLocalDiscount
     *
     * @return string
     */
    public function getTotalLocalDiscount()
    {
        return $this->totalLocalDiscount;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return FinVendorInvoice
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;

        return $this;
    }

    /**
     * Get reversalBlocked
     *
     * @return boolean
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return FinVendorInvoice
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set discountRate
     *
     * @param string $discountRate
     *
     * @return FinVendorInvoice
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return string
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set discountAmount
     *
     * @param string $discountAmount
     *
     * @return FinVendorInvoice
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * Get discountAmount
     *
     * @return string
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return FinVendorInvoice
     */
    public function setVendor(\Application\Entity\NmtBpVendor $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\NmtBpVendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set procureGr
     *
     * @param \Application\Entity\NmtProcureGr $procureGr
     *
     * @return FinVendorInvoice
     */
    public function setProcureGr(\Application\Entity\NmtProcureGr $procureGr = null)
    {
        $this->procureGr = $procureGr;

        return $this;
    }

    /**
     * Get procureGr
     *
     * @return \Application\Entity\NmtProcureGr
     */
    public function getProcureGr()
    {
        return $this->procureGr;
    }

    /**
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return FinVendorInvoice
     */
    public function setLocalCurrency(\Application\Entity\NmtApplicationCurrency $localCurrency = null)
    {
        $this->localCurrency = $localCurrency;

        return $this;
    }

    /**
     * Get localCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return FinVendorInvoice
     */
    public function setDocCurrency(\Application\Entity\NmtApplicationCurrency $docCurrency = null)
    {
        $this->docCurrency = $docCurrency;

        return $this;
    }

    /**
     * Get docCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return FinVendorInvoice
     */
    public function setPostingPeriod(\Application\Entity\NmtFinPostingPeriod $postingPeriod = null)
    {
        $this->postingPeriod = $postingPeriod;

        return $this;
    }

    /**
     * Get postingPeriod
     *
     * @return \Application\Entity\NmtFinPostingPeriod
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     * Set incoterm2
     *
     * @param \Application\Entity\NmtApplicationIncoterms $incoterm2
     *
     * @return FinVendorInvoice
     */
    public function setIncoterm2(\Application\Entity\NmtApplicationIncoterms $incoterm2 = null)
    {
        $this->incoterm2 = $incoterm2;

        return $this;
    }

    /**
     * Get incoterm2
     *
     * @return \Application\Entity\NmtApplicationIncoterms
     */
    public function getIncoterm2()
    {
        return $this->incoterm2;
    }

    /**
     * Set pmtTerm
     *
     * @param \Application\Entity\NmtApplicationPmtTerm $pmtTerm
     *
     * @return FinVendorInvoice
     */
    public function setPmtTerm(\Application\Entity\NmtApplicationPmtTerm $pmtTerm = null)
    {
        $this->pmtTerm = $pmtTerm;

        return $this;
    }

    /**
     * Get pmtTerm
     *
     * @return \Application\Entity\NmtApplicationPmtTerm
     */
    public function getPmtTerm()
    {
        return $this->pmtTerm;
    }

    /**
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return FinVendorInvoice
     */
    public function setWarehouse(\Application\Entity\NmtInventoryWarehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinVendorInvoice
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return FinVendorInvoice
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return FinVendorInvoice
     */
    public function setCurrency(\Application\Entity\NmtApplicationCurrency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set po
     *
     * @param \Application\Entity\NmtProcurePo $po
     *
     * @return FinVendorInvoice
     */
    public function setPo(\Application\Entity\NmtProcurePo $po = null)
    {
        $this->po = $po;

        return $this;
    }

    /**
     * Get po
     *
     * @return \Application\Entity\NmtProcurePo
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return FinVendorInvoice
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set paymentMethod
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $paymentMethod
     *
     * @return FinVendorInvoice
     */
    public function setPaymentMethod(\Application\Entity\NmtApplicationPmtMethod $paymentMethod = null)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return \Application\Entity\NmtApplicationPmtMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set inventoryGr
     *
     * @param \Application\Entity\NmtInventoryGr $inventoryGr
     *
     * @return FinVendorInvoice
     */
    public function setInventoryGr(\Application\Entity\NmtInventoryGr $inventoryGr = null)
    {
        $this->inventoryGr = $inventoryGr;

        return $this;
    }

    /**
     * Get inventoryGr
     *
     * @return \Application\Entity\NmtInventoryGr
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }
}
