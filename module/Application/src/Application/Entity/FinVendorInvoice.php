<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinVendorInvoice
 *
 * @ORM\Table(name="fin_vendor_invoice", indexes={@ORM\Index(name="fin_vendor_invoice_FK1_idx", columns={"vendor_id"}), @ORM\Index(name="fin_vendor_invoice_FK2_idx", columns={"warehouse_id"}), @ORM\Index(name="fin_vendor_invoice_FK3_idx", columns={"created_by"}), @ORM\Index(name="fin_vendor_invoice_FK5_idx", columns={"lastchange_by"}), @ORM\Index(name="fin_vendor_invoice_FK5_idx1", columns={"currency_id"}), @ORM\Index(name="fin_vendor_invoice_FK6_idx", columns={"po_id"}), @ORM\Index(name="fin_vendor_invoice_FK7_idx", columns={"company_id"})})
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
     * @ORM\Column(name="exchange_rate", type="decimal", precision=8, scale=2, nullable=true)
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
}
