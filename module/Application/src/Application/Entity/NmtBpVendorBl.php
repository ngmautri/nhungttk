<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtBpVendorBl
 *
 * @ORM\Table(name="nmt_bp_vendor_bl", indexes={@ORM\Index(name="nmt_bp_vendor_bl_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_bp_vendor_bl_FK2_idx", columns={"company_id"}), @ORM\Index(name="nmt_bp_vendor_bl_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_bp_vendor_bl_FK4_idx", columns={"GL_account_id"}), @ORM\Index(name="nmt_bp_vendor_bl_FK5_idx", columns={"posting_period_id"}), @ORM\Index(name="nmt_bp_vendor_bl_FK6_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtBpVendorBl
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
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_number", type="string", length=45, nullable=true)
     */
    private $docNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="currency_id", type="integer", nullable=true)
     */
    private $currencyId;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=45, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="posting_code", type="string", length=5, nullable=true)
     */
    private $postingCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="document_date", type="datetime", nullable=true)
     */
    private $documentDate;

    /**
     * @var string
     *
     * @ORM\Column(name="local_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $localAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="clearing_doc", type="string", length=45, nullable=true)
     */
    private $clearingDoc;

    /**
     * @var integer
     *
     * @ORM\Column(name="clearing_doc_id", type="integer", nullable=true)
     */
    private $clearingDocId;

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
     * @var string
     *
     * @ORM\Column(name="revision_no", type="string", length=45, nullable=true)
     */
    private $revisionNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="row_number", type="integer", nullable=true)
     */
    private $rowNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=45, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

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
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;



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
     * @return NmtBpVendorBl
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
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtBpVendorBl
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
     * Set docNumber
     *
     * @param string $docNumber
     *
     * @return NmtBpVendorBl
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;

        return $this;
    }

    /**
     * Get docNumber
     *
     * @return string
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     * Set currencyId
     *
     * @param integer $currencyId
     *
     * @return NmtBpVendorBl
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return integer
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return NmtBpVendorBl
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set postingCode
     *
     * @param string $postingCode
     *
     * @return NmtBpVendorBl
     */
    public function setPostingCode($postingCode)
    {
        $this->postingCode = $postingCode;

        return $this;
    }

    /**
     * Get postingCode
     *
     * @return string
     */
    public function getPostingCode()
    {
        return $this->postingCode;
    }

    /**
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return NmtBpVendorBl
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
     * Set documentDate
     *
     * @param \DateTime $documentDate
     *
     * @return NmtBpVendorBl
     */
    public function setDocumentDate($documentDate)
    {
        $this->documentDate = $documentDate;

        return $this;
    }

    /**
     * Get documentDate
     *
     * @return \DateTime
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * Set localAmount
     *
     * @param string $localAmount
     *
     * @return NmtBpVendorBl
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
     * @return NmtBpVendorBl
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
     * Set clearingDoc
     *
     * @param string $clearingDoc
     *
     * @return NmtBpVendorBl
     */
    public function setClearingDoc($clearingDoc)
    {
        $this->clearingDoc = $clearingDoc;

        return $this;
    }

    /**
     * Get clearingDoc
     *
     * @return string
     */
    public function getClearingDoc()
    {
        return $this->clearingDoc;
    }

    /**
     * Set clearingDocId
     *
     * @param integer $clearingDocId
     *
     * @return NmtBpVendorBl
     */
    public function setClearingDocId($clearingDocId)
    {
        $this->clearingDocId = $clearingDocId;

        return $this;
    }

    /**
     * Get clearingDocId
     *
     * @return integer
     */
    public function getClearingDocId()
    {
        return $this->clearingDocId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtBpVendorBl
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
     * @return NmtBpVendorBl
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
     * @param string $revisionNo
     *
     * @return NmtBpVendorBl
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return string
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set rowNumber
     *
     * @param integer $rowNumber
     *
     * @return NmtBpVendorBl
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    /**
     * Get rowNumber
     *
     * @return integer
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return NmtBpVendorBl
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
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtBpVendorBl
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtBpVendorBl
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
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return NmtBpVendorBl
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
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtBpVendorBl
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtBpVendorBl
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtBpVendorBl
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
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return NmtBpVendorBl
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return NmtBpVendorBl
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtBpVendorBl
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
}
