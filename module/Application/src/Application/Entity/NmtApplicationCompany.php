<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationCompany
 *
 * @ORM\Table(name="nmt_application_company", uniqueConstraints={@ORM\UniqueConstraint(name="nmt_application_company_company_name_UNIQUE", columns={"company_name"}), @ORM\UniqueConstraint(name="company_code_UNIQUE", columns={"company_code"})}, indexes={@ORM\Index(name="nmt_application_company_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_company_FK1_idx", columns={"default_currency_id"}), @ORM\Index(name="nmt_application_company_FK4_idx", columns={"default_address_id"})})
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
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;

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
     * @var \Application\Entity\NmtApplicationCompanyAddress
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompanyAddress")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_address_id", referencedColumnName="id")
     * })
     */
    private $defaultAddress;



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
}
