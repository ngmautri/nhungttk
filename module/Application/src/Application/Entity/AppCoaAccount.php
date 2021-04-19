<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppCoaAccount
 *
 * @ORM\Table(name="app_coa_account", indexes={@ORM\Index(name="app_coa_account_FK1_idx", columns={"coa_id"}), @ORM\Index(name="app_coa_account_FK2_idx", columns={"created_by"}), @ORM\Index(name="app_coa_account_FK3_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class AppCoaAccount
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
     * @ORM\Column(name="uuid", type="string", length=38, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=38, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="account_numer", type="string", length=40, nullable=true)
     */
    private $accountNumer;

    /**
     * @var string
     *
     * @ORM\Column(name="account_name", type="string", length=45, nullable=true)
     */
    private $accountName;

    /**
     * @var string
     *
     * @ORM\Column(name="account_type", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="account_group", type="string", length=45, nullable=true)
     */
    private $accountGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_account_number", type="string", length=45, nullable=true)
     */
    private $parentAccountNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_reconciliation", type="boolean", nullable=true)
     */
    private $allowReconciliation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_cost_center", type="boolean", nullable=true)
     */
    private $hasCostCenter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_clearing_account", type="boolean", nullable=true)
     */
    private $isClearingAccount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_control_account", type="boolean", nullable=true)
     */
    private $isControlAccount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="manual_posting_blocked", type="boolean", nullable=true)
     */
    private $manualPostingBlocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_posting", type="boolean", nullable=true)
     */
    private $allowPosting;

    /**
     * @var string
     *
     * @ORM\Column(name="account_number", type="string", length=45, nullable=true)
     */
    private $accountNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="account_feature", type="string", length=45, nullable=true)
     */
    private $accountFeature;

    /**
     * @var string
     *
     * @ORM\Column(name="control_for", type="string", length=45, nullable=true)
     */
    private $controlFor;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var integer
     *
     * @ORM\Column(name="revisionNo", type="integer", nullable=true)
     */
    private $revisionno;

    /**
     * @var \Application\Entity\AppCoa
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\AppCoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coa_id", referencedColumnName="id")
     * })
     */
    private $coa;

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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return AppCoaAccount
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
     * Set token
     *
     * @param string $token
     *
     * @return AppCoaAccount
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
     * Set accountNumer
     *
     * @param string $accountNumer
     *
     * @return AppCoaAccount
     */
    public function setAccountNumer($accountNumer)
    {
        $this->accountNumer = $accountNumer;

        return $this;
    }

    /**
     * Get accountNumer
     *
     * @return string
     */
    public function getAccountNumer()
    {
        return $this->accountNumer;
    }

    /**
     * Set accountName
     *
     * @param string $accountName
     *
     * @return AppCoaAccount
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
     * Set accountType
     *
     * @param string $accountType
     *
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
     * Set parentAccountNumber
     *
     * @param string $parentAccountNumber
     *
     * @return AppCoaAccount
     */
    public function setParentAccountNumber($parentAccountNumber)
    {
        $this->parentAccountNumber = $parentAccountNumber;

        return $this;
    }

    /**
     * Get parentAccountNumber
     *
     * @return string
     */
    public function getParentAccountNumber()
    {
        return $this->parentAccountNumber;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return AppCoaAccount
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
     * Set allowReconciliation
     *
     * @param boolean $allowReconciliation
     *
     * @return AppCoaAccount
     */
    public function setAllowReconciliation($allowReconciliation)
    {
        $this->allowReconciliation = $allowReconciliation;

        return $this;
    }

    /**
     * Get allowReconciliation
     *
     * @return boolean
     */
    public function getAllowReconciliation()
    {
        return $this->allowReconciliation;
    }

    /**
     * Set hasCostCenter
     *
     * @param boolean $hasCostCenter
     *
     * @return AppCoaAccount
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
     * Set isClearingAccount
     *
     * @param boolean $isClearingAccount
     *
     * @return AppCoaAccount
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
     * Set isControlAccount
     *
     * @param boolean $isControlAccount
     *
     * @return AppCoaAccount
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
     * Set manualPostingBlocked
     *
     * @param boolean $manualPostingBlocked
     *
     * @return AppCoaAccount
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
     * Set allowPosting
     *
     * @param boolean $allowPosting
     *
     * @return AppCoaAccount
     */
    public function setAllowPosting($allowPosting)
    {
        $this->allowPosting = $allowPosting;

        return $this;
    }

    /**
     * Get allowPosting
     *
     * @return boolean
     */
    public function getAllowPosting()
    {
        return $this->allowPosting;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     *
     * @return AppCoaAccount
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set accountFeature
     *
     * @param string $accountFeature
     *
     * @return AppCoaAccount
     */
    public function setAccountFeature($accountFeature)
    {
        $this->accountFeature = $accountFeature;

        return $this;
    }

    /**
     * Get accountFeature
     *
     * @return string
     */
    public function getAccountFeature()
    {
        return $this->accountFeature;
    }

    /**
     * Set controlFor
     *
     * @param string $controlFor
     *
     * @return AppCoaAccount
     */
    public function setControlFor($controlFor)
    {
        $this->controlFor = $controlFor;

        return $this;
    }

    /**
     * Get controlFor
     *
     * @return string
     */
    public function getControlFor()
    {
        return $this->controlFor;
    }

    /**
     * Set version
     *
     * @param integer $version
     *
     * @return AppCoaAccount
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set revisionno
     *
     * @param integer $revisionno
     *
     * @return AppCoaAccount
     */
    public function setRevisionno($revisionno)
    {
        $this->revisionno = $revisionno;

        return $this;
    }

    /**
     * Get revisionno
     *
     * @return integer
     */
    public function getRevisionno()
    {
        return $this->revisionno;
    }

    /**
     * Set coa
     *
     * @param \Application\Entity\AppCoa $coa
     *
     * @return AppCoaAccount
     */
    public function setCoa(\Application\Entity\AppCoa $coa = null)
    {
        $this->coa = $coa;

        return $this;
    }

    /**
     * Get coa
     *
     * @return \Application\Entity\AppCoa
     */
    public function getCoa()
    {
        return $this->coa;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return AppCoaAccount
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
     * @return AppCoaAccount
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
