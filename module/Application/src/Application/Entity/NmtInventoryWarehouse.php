<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWarehouse
 *
 * @ORM\Table(name="nmt_inventory_warehouse", indexes={@ORM\Index(name="nmt_inventory_warehouse_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_warehouse_FK2_idx", columns={"company_id"}), @ORM\Index(name="nmt_inventory_warehouse_FK3_idx", columns={"wh_country"})})
 * @ORM\Entity
 */
class NmtInventoryWarehouse
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
     * @ORM\Column(name="wh_code", type="string", length=45, nullable=false)
     */
    private $whCode;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_name", type="string", length=100, nullable=false)
     */
    private $whName;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_address", type="string", length=100, nullable=true)
     */
    private $whAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_contact_person", type="string", length=45, nullable=true)
     */
    private $whContactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_telephone", type="string", length=45, nullable=true)
     */
    private $whTelephone;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_email", type="string", length=45, nullable=true)
     */
    private $whEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(name="wh_status", type="string", length=45, nullable=true)
     */
    private $whStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

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
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var \Application\Entity\NmtApplicationCountry
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCountry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wh_country", referencedColumnName="id")
     * })
     */
    private $whCountry;



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
     * Set whCode
     *
     * @param string $whCode
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhCode($whCode)
    {
        $this->whCode = $whCode;

        return $this;
    }

    /**
     * Get whCode
     *
     * @return string
     */
    public function getWhCode()
    {
        return $this->whCode;
    }

    /**
     * Set whName
     *
     * @param string $whName
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhName($whName)
    {
        $this->whName = $whName;

        return $this;
    }

    /**
     * Get whName
     *
     * @return string
     */
    public function getWhName()
    {
        return $this->whName;
    }

    /**
     * Set whAddress
     *
     * @param string $whAddress
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhAddress($whAddress)
    {
        $this->whAddress = $whAddress;

        return $this;
    }

    /**
     * Get whAddress
     *
     * @return string
     */
    public function getWhAddress()
    {
        return $this->whAddress;
    }

    /**
     * Set whContactPerson
     *
     * @param string $whContactPerson
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhContactPerson($whContactPerson)
    {
        $this->whContactPerson = $whContactPerson;

        return $this;
    }

    /**
     * Get whContactPerson
     *
     * @return string
     */
    public function getWhContactPerson()
    {
        return $this->whContactPerson;
    }

    /**
     * Set whTelephone
     *
     * @param string $whTelephone
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhTelephone($whTelephone)
    {
        $this->whTelephone = $whTelephone;

        return $this;
    }

    /**
     * Get whTelephone
     *
     * @return string
     */
    public function getWhTelephone()
    {
        return $this->whTelephone;
    }

    /**
     * Set whEmail
     *
     * @param string $whEmail
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhEmail($whEmail)
    {
        $this->whEmail = $whEmail;

        return $this;
    }

    /**
     * Get whEmail
     *
     * @return string
     */
    public function getWhEmail()
    {
        return $this->whEmail;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return NmtInventoryWarehouse
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set whStatus
     *
     * @param string $whStatus
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhStatus($whStatus)
    {
        $this->whStatus = $whStatus;

        return $this;
    }

    /**
     * Get whStatus
     *
     * @return string
     */
    public function getWhStatus()
    {
        return $this->whStatus;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryWarehouse
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
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return NmtInventoryWarehouse
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryWarehouse
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryWarehouse
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtInventoryWarehouse
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
     * Set whCountry
     *
     * @param \Application\Entity\NmtApplicationCountry $whCountry
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhCountry(\Application\Entity\NmtApplicationCountry $whCountry = null)
    {
        $this->whCountry = $whCountry;

        return $this;
    }

    /**
     * Get whCountry
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getWhCountry()
    {
        return $this->whCountry;
    }
}
