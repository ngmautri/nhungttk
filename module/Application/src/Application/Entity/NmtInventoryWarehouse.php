<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWarehouse
 *
 * @ORM\Table(name="nmt_inventory_warehouse", indexes={@ORM\Index(name="nmt_inventory_warehouse_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_warehouse_FK2_idx", columns={"company_id"}), @ORM\Index(name="nmt_inventory_warehouse_FK3_idx", columns={"wh_country"}), @ORM\Index(name="nmt_inventory_warehouse_FK4_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_warehouse_FK5_idx", columns={"stockkeeper_id"}), @ORM\Index(name="nmt_inventory_warehouse_FK6_idx", columns={"wh_controller_id"}), @ORM\Index(name="nmt_inventory_warehouse_FK7_idx", columns={"location_id"})})
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
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stockkeeper_id", referencedColumnName="id")
     * })
     */
    private $stockkeeper;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wh_controller_id", referencedColumnName="id")
     * })
     */
    private $whController;

    /**
     * @var \Application\Entity\NmtInventoryWarehouseLocation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouseLocation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * })
     */
    private $location;



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
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryWarehouse
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryWarehouse
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
     * @return NmtInventoryWarehouse
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
     * @return NmtInventoryWarehouse
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
     * @return NmtInventoryWarehouse
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

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryWarehouse
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
     * Set stockkeeper
     *
     * @param \Application\Entity\MlaUsers $stockkeeper
     *
     * @return NmtInventoryWarehouse
     */
    public function setStockkeeper(\Application\Entity\MlaUsers $stockkeeper = null)
    {
        $this->stockkeeper = $stockkeeper;

        return $this;
    }

    /**
     * Get stockkeeper
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getStockkeeper()
    {
        return $this->stockkeeper;
    }

    /**
     * Set whController
     *
     * @param \Application\Entity\MlaUsers $whController
     *
     * @return NmtInventoryWarehouse
     */
    public function setWhController(\Application\Entity\MlaUsers $whController = null)
    {
        $this->whController = $whController;

        return $this;
    }

    /**
     * Get whController
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getWhController()
    {
        return $this->whController;
    }

    /**
     * Set location
     *
     * @param \Application\Entity\NmtInventoryWarehouseLocation $location
     *
     * @return NmtInventoryWarehouse
     */
    public function setLocation(\Application\Entity\NmtInventoryWarehouseLocation $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Application\Entity\NmtInventoryWarehouseLocation
     */
    public function getLocation()
    {
        return $this->location;
    }
}
