<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PmtOutgoing
 *
 * @ORM\Table(name="pmt_outgoing", indexes={@ORM\Index(name="pmt_outgoing_FK1_idx", columns={"doc_currency_id"}), @ORM\Index(name="pmt_outgoing_FK2_idx", columns={"pmt_method_id"}), @ORM\Index(name="pmt_outgoing_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="pmt_outgoing_FK4_idx", columns={"ap_invoice_id"}), @ORM\Index(name="pmt_outgoing_FK5_idx", columns={"created_by"}), @ORM\Index(name="pmt_outgoing_FK6_idx", columns={"last_change_by"}), @ORM\Index(name="pmt_outgoing_FK7_idx", columns={"local_currency_id"}), @ORM\Index(name="pmt_outgoing_FK8_idx", columns={"posting_period"}), @ORM\Index(name="pmt_outgoing_FK8_idx1", columns={"po_id"})})
 * @ORM\Entity
 */
class PmtOutgoing
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
     * @var \DateTime
     *
     * @ORM\Column(name="doc_date", type="datetime", nullable=true)
     */
    private $docDate;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=10, nullable=true)
     */
    private $docStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var string
     *
     * @ORM\Column(name="local_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $localAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $docAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_name", type="string", length=45, nullable=true)
     */
    private $vendorName;

    /**
     * @var string
     *
     * @ORM\Column(name="target_document", type="string", nullable=true)
     */
    private $targetDocument;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer", nullable=true)
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="target_class", type="string", length=45, nullable=true)
     */
    private $targetClass;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="discount_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $discountAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="sap_doc", type="string", length=45, nullable=true)
     */
    private $sapDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=200, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversed", type="boolean", nullable=true)
     */
    private $isReversed;

    /**
     * @var string
     *
     * @ORM\Column(name="posting_key", type="string", length=1, nullable=true)
     */
    private $postingKey;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_currency_id", referencedColumnName="id")
     * })
     */
    private $docCurrency;

    /**
     * @var \Application\Entity\NmtApplicationPmtMethod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pmt_method_id", referencedColumnName="id")
     * })
     */
    private $pmtMethod;

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
     * @var \Application\Entity\FinVendorInvoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_invoice_id", referencedColumnName="id")
     * })
     */
    private $apInvoice;

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
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

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
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posting_period", referencedColumnName="id")
     * })
     */
    private $postingPeriod;

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
     * @return PmtOutgoing
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
     * Set docDate
     *
     * @param \DateTime $docDate
     *
     * @return PmtOutgoing
     */
    public function setDocDate($docDate)
    {
        $this->docDate = $docDate;

        return $this;
    }

    /**
     * Get docDate
     *
     * @return \DateTime
     */
    public function getDocDate()
    {
        return $this->docDate;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return PmtOutgoing
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
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return PmtOutgoing
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
     * Set localAmount
     *
     * @param string $localAmount
     *
     * @return PmtOutgoing
     */
    public function setLocalAmount($localAmount)
    {
        $this->localAmount = $localAmount;

        return $this;
    }

    /**
     * Get localAmount
     *
     * @return string
     */
    public function getLocalAmount()
    {
        return $this->localAmount;
    }

    /**
     * Set docAmount
     *
     * @param string $docAmount
     *
     * @return PmtOutgoing
     */
    public function setDocAmount($docAmount)
    {
        $this->docAmount = $docAmount;

        return $this;
    }

    /**
     * Get docAmount
     *
     * @return string
     */
    public function getDocAmount()
    {
        return $this->docAmount;
    }

    /**
     * Set vendorName
     *
     * @param string $vendorName
     *
     * @return PmtOutgoing
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
     * Set targetDocument
     *
     * @param string $targetDocument
     *
     * @return PmtOutgoing
     */
    public function setTargetDocument($targetDocument)
    {
        $this->targetDocument = $targetDocument;

        return $this;
    }

    /**
     * Get targetDocument
     *
     * @return string
     */
    public function getTargetDocument()
    {
        return $this->targetDocument;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return PmtOutgoing
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set targetClass
     *
     * @param string $targetClass
     *
     * @return PmtOutgoing
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;

        return $this;
    }

    /**
     * Get targetClass
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PmtOutgoing
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return PmtOutgoing
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return PmtOutgoing
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
     * Set discountAmount
     *
     * @param string $discountAmount
     *
     * @return PmtOutgoing
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
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return PmtOutgoing
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return PmtOutgoing
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
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return PmtOutgoing
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
     * Set sapDoc
     *
     * @param string $sapDoc
     *
     * @return PmtOutgoing
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return PmtOutgoing
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return PmtOutgoing
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
     * Set docType
     *
     * @param string $docType
     *
     * @return PmtOutgoing
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
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return PmtOutgoing
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
     * Set postingKey
     *
     * @param string $postingKey
     *
     * @return PmtOutgoing
     */
    public function setPostingKey($postingKey)
    {
        $this->postingKey = $postingKey;

        return $this;
    }

    /**
     * Get postingKey
     *
     * @return string
     */
    public function getPostingKey()
    {
        return $this->postingKey;
    }

    /**
     * Set reversalDate
     *
     * @param \DateTime $reversalDate
     *
     * @return PmtOutgoing
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
     * @return PmtOutgoing
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
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return PmtOutgoing
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
     * Set pmtMethod
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $pmtMethod
     *
     * @return PmtOutgoing
     */
    public function setPmtMethod(\Application\Entity\NmtApplicationPmtMethod $pmtMethod = null)
    {
        $this->pmtMethod = $pmtMethod;

        return $this;
    }

    /**
     * Get pmtMethod
     *
     * @return \Application\Entity\NmtApplicationPmtMethod
     */
    public function getPmtMethod()
    {
        return $this->pmtMethod;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return PmtOutgoing
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
     * Set apInvoice
     *
     * @param \Application\Entity\FinVendorInvoice $apInvoice
     *
     * @return PmtOutgoing
     */
    public function setApInvoice(\Application\Entity\FinVendorInvoice $apInvoice = null)
    {
        $this->apInvoice = $apInvoice;

        return $this;
    }

    /**
     * Get apInvoice
     *
     * @return \Application\Entity\FinVendorInvoice
     */
    public function getApInvoice()
    {
        return $this->apInvoice;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return PmtOutgoing
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return PmtOutgoing
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return PmtOutgoing
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
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return PmtOutgoing
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
     * Set po
     *
     * @param \Application\Entity\NmtProcurePo $po
     *
     * @return PmtOutgoing
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
}
