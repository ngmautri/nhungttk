<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinAccount
 *
 * @ORM\Table(name="fin_account", indexes={@ORM\Index(name="fin_account_FK1_idx", columns={"company_id"}), @ORM\Index(name="fin_account_FK2_idx", columns={"created_by"}), @ORM\Index(name="fin_account_FK3_idx", columns={"last_change_by"}), @ORM\Index(name="fin_account_idx1", columns={"token"})})
 * @ORM\Entity
 */
class FinAccount
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
     * @var integer
     *
     * @ORM\Column(name="account_number", type="integer", nullable=true)
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="account_type", type="string", length=10, nullable=true)
     */
    private $accountType;

    /**
     * @var string
     *
     * @ORM\Column(name="account_class", type="string", length=45, nullable=true)
     */
    private $accountClass;

    /**
     * @var string
     *
     * @ORM\Column(name="account_group", type="string", length=10, nullable=true)
     */
    private $accountGroup;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

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
     * @ORM\Column(name="account_name", type="string", length=45, nullable=true)
     */
    private $accountName;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="reconcilation_for", type="string", length=10, nullable=true)
     */
    private $reconcilationFor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="manual_posting_blocked", type="boolean", nullable=true)
     */
    private $manualPostingBlocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_control_account", type="boolean", nullable=true)
     */
    private $isControlAccount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_clearing_account", type="boolean", nullable=true)
     */
    private $isClearingAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="sap_account", type="string", length=45, nullable=true)
     */
    private $sapAccount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_cost_center", type="boolean", nullable=true)
     */
    private $hasCostCenter;

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
     * @return FinAccount
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
     * Set accountNumber
     *
     * @param integer $accountNumber
     *
     * @return FinAccount
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return integer
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set accountType
     *
     * @param string $accountType
     *
     * @return FinAccount
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Get accountType
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * Set accountClass
     *
     * @param string $accountClass
     *
     * @return FinAccount
     */
    public function setAccountClass($accountClass)
    {
        $this->accountClass = $accountClass;

        return $this;
    }

    /**
     * Get accountClass
     *
     * @return string
     */
    public function getAccountClass()
    {
        return $this->accountClass;
    }

    /**
     * Set accountGroup
     *
     * @param string $accountGroup
     *
     * @return FinAccount
     */
    public function setAccountGroup($accountGroup)
    {
        $this->accountGroup = $accountGroup;

        return $this;
    }

    /**
     * Get accountGroup
     *
     * @return string
     */
    public function getAccountGroup()
    {
        return $this->accountGroup;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return FinAccount
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
     * Set description
     *
     * @param string $description
     *
     * @return FinAccount
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinAccount
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
     * @return FinAccount
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
     * @return FinAccount
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
     * Set accountName
     *
     * @param string $accountName
     *
     * @return FinAccount
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;

        return $this;
    }

    /**
     * Get accountName
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return FinAccount
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
     * Set reconcilationFor
     *
     * @param string $reconcilationFor
     *
     * @return FinAccount
     */
    public function setReconcilationFor($reconcilationFor)
    {
        $this->reconcilationFor = $reconcilationFor;

        return $this;
    }

    /**
     * Get reconcilationFor
     *
     * @return string
     */
    public function getReconcilationFor()
    {
        return $this->reconcilationFor;
    }

    /**
     * Set manualPostingBlocked
     *
     * @param boolean $manualPostingBlocked
     *
     * @return FinAccount
     */
    public function setManualPostingBlocked($manualPostingBlocked)
    {
        $this->manualPostingBlocked = $manualPostingBlocked;

        return $this;
    }

    /**
     * Get manualPostingBlocked
     *
     * @return boolean
     */
    public function getManualPostingBlocked()
    {
        return $this->manualPostingBlocked;
    }

    /**
     * Set isControlAccount
     *
     * @param boolean $isControlAccount
     *
     * @return FinAccount
     */
    public function setIsControlAccount($isControlAccount)
    {
        $this->isControlAccount = $isControlAccount;

        return $this;
    }

    /**
     * Get isControlAccount
     *
     * @return boolean
     */
    public function getIsControlAccount()
    {
        return $this->isControlAccount;
    }

    /**
     * Set isClearingAccount
     *
     * @param boolean $isClearingAccount
     *
     * @return FinAccount
     */
    public function setIsClearingAccount($isClearingAccount)
    {
        $this->isClearingAccount = $isClearingAccount;

        return $this;
    }

    /**
     * Get isClearingAccount
     *
     * @return boolean
     */
    public function getIsClearingAccount()
    {
        return $this->isClearingAccount;
    }

    /**
     * Set sapAccount
     *
     * @param string $sapAccount
     *
     * @return FinAccount
     */
    public function setSapAccount($sapAccount)
    {
        $this->sapAccount = $sapAccount;

        return $this;
    }

    /**
     * Get sapAccount
     *
     * @return string
     */
    public function getSapAccount()
    {
        return $this->sapAccount;
    }

    /**
     * Set hasCostCenter
     *
     * @param boolean $hasCostCenter
     *
     * @return FinAccount
     */
    public function setHasCostCenter($hasCostCenter)
    {
        $this->hasCostCenter = $hasCostCenter;

        return $this;
    }

    /**
     * Get hasCostCenter
     *
     * @return boolean
     */
    public function getHasCostCenter()
    {
        return $this->hasCostCenter;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return FinAccount
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
     * @return FinAccount
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
     * @return FinAccount
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
