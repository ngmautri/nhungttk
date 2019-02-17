<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePo
 *
 * @ORM\Table(name="nmt_procure_po", indexes={@ORM\Index(name="nmt_procure_po_FK1_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_procure_po_FK2_idx", columns={"warehouse_id"}), @ORM\Index(name="nmt_procure_po_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_po_FK5_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_procure_po_FK5_idx1", columns={"currency_id"}), @ORM\Index(name="nmt_procure_po_FK6_idx", columns={"payment_method"}), @ORM\Index(name="nmt_procure_po_FK7_idx", columns={"token"}), @ORM\Index(name="nmt_procure_po_FK7_idx1", columns={"local_currency_id"}), @ORM\Index(name="nmt_procure_po_FK8_idx", columns={"doc_currency_id"}), @ORM\Index(name="nmt_procure_po_FK9_idx", columns={"incoterm_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePoRepository")
 */
class NmtProcurePo
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
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
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
     * @ORM\Column(name="delivery_mode", type="string", length=45, nullable=true)
     */
    private $deliveryMode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="incoterm", type="string", length=45, nullable=true)
     */
    private $incoterm;
    
    /**
     * @var string
     *
     * @ORM\Column(name="incoterm_place", type="string", length=100, nullable=true)
     */
    private $incotermPlace;
    
    /**
     * @var string
     *
     * @ORM\Column(name="payment_term", type="string", length=45, nullable=true)
     */
    private $paymentTerm;
    
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
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="doc_type", type="smallint", nullable=true)
     */
    private $docType;
    
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
     * @var \Application\Entity\NmtApplicationPmtMethod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="payment_method", referencedColumnName="id")
     * })
     */
    private $paymentMethod;
    
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
     * @var \Application\Entity\NmtApplicationIncoterms
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationIncoterms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="incoterm_id", referencedColumnName="id")
     * })
     */
    private $incoterm2;
    
    
    
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * Set deliveryMode
     *
     * @param string $deliveryMode
     *
     * @return NmtProcurePo
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;
        
        return $this;
    }
    
    /**
     * Get deliveryMode
     *
     * @return string
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }
    
    /**
     * Set incoterm
     *
     * @param string $incoterm
     *
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * Set paymentTerm
     *
     * @param string $paymentTerm
     *
     * @return NmtProcurePo
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
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return NmtProcurePo
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
     * Set docType
     *
     * @param integer $docType
     *
     * @return NmtProcurePo
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
        
        return $this;
    }
    
    /**
     * Get docType
     *
     * @return integer
     */
    public function getDocType()
    {
        return $this->docType;
    }
    
    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtProcurePo
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
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * Set paymentMethod
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $paymentMethod
     *
     * @return NmtProcurePo
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
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return NmtProcurePo
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
     * @return NmtProcurePo
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
     * Set incoterm2
     *
     * @param \Application\Entity\NmtApplicationIncoterms $incoterm2
     *
     * @return NmtProcurePo
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
}
