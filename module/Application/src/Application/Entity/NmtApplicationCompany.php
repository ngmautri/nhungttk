<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationCompany
 *
 * @ORM\Table(name="nmt_application_company", indexes={@ORM\Index(name="nmt_application_company_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_company_FK1_idx", columns={"default_currency_id"}), @ORM\Index(name="nmt_application_company_FK4_idx", columns={"default_address_id"}), @ORM\Index(name="nmt_application_company_IDX1", columns={"token"}), @ORM\Index(name="nmt_application_company_FK3_idx", columns={"country_id"}), @ORM\Index(name="nmt_application_company_FK5_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_application_company_FK6_idx", columns={"default_warehouse"})})
 * @ORM\Entity
 */
class NmtApplicationCompany
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
     * @var integer
     *
     * @ORM\Column(name="company_code", type="integer", nullable=false)
     */
    private $companyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=100, nullable=false)
     */
    private $companyName;

    /**
     * @var integer
     *
     * @ORM\Column(name="default_logo_id", type="integer", nullable=true)
     */
    private $defaultLogoId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

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
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_currency_id", referencedColumnName="id")
     * })
     */
    private $defaultCurrency;

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
     * @var \Application\Entity\NmtApplicationCountry
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCountry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

    /**
     * @var \Application\Entity\NmtApplicationCompanyAddress
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompanyAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_address_id", referencedColumnName="id")
     * })
     */
    private $defaultAddress;

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
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_warehouse", referencedColumnName="id")
     * })
     */
    private $defaultWarehouse;



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
     * Set companyCode
     *
     * @param integer $companyCode
     *
     * @return NmtApplicationCompany
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;

        return $this;
    }

    /**
     * Get companyCode
     *
     * @return integer
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return NmtApplicationCompany
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set defaultLogoId
     *
     * @param integer $defaultLogoId
     *
     * @return NmtApplicationCompany
     */
    public function setDefaultLogoId($defaultLogoId)
    {
        $this->defaultLogoId = $defaultLogoId;

        return $this;
    }

    /**
     * Get defaultLogoId
     *
     * @return integer
     */
    public function getDefaultLogoId()
    {
        return $this->defaultLogoId;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationCompany
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationCompany
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
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return NmtApplicationCompany
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtApplicationCompany
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtApplicationCompany
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
     * @return NmtApplicationCompany
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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtApplicationCompany
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
     * Set defaultCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $defaultCurrency
     *
     * @return NmtApplicationCompany
     */
    public function setDefaultCurrency(\Application\Entity\NmtApplicationCurrency $defaultCurrency = null)
    {
        $this->defaultCurrency = $defaultCurrency;

        return $this;
    }

    /**
     * Get defaultCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationCompany
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
     * Set country
     *
     * @param \Application\Entity\NmtApplicationCountry $country
     *
     * @return NmtApplicationCompany
     */
    public function setCountry(\Application\Entity\NmtApplicationCountry $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set defaultAddress
     *
     * @param \Application\Entity\NmtApplicationCompanyAddress $defaultAddress
     *
     * @return NmtApplicationCompany
     */
    public function setDefaultAddress(\Application\Entity\NmtApplicationCompanyAddress $defaultAddress = null)
    {
        $this->defaultAddress = $defaultAddress;

        return $this;
    }

    /**
     * Get defaultAddress
     *
     * @return \Application\Entity\NmtApplicationCompanyAddress
     */
    public function getDefaultAddress()
    {
        return $this->defaultAddress;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtApplicationCompany
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
     * Set defaultWarehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $defaultWarehouse
     *
     * @return NmtApplicationCompany
     */
    public function setDefaultWarehouse(\Application\Entity\NmtInventoryWarehouse $defaultWarehouse = null)
    {
        $this->defaultWarehouse = $defaultWarehouse;

        return $this;
    }

    /**
     * Get defaultWarehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }
}
