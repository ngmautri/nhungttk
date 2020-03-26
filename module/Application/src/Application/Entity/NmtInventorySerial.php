<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventorySerial
 *
 * @ORM\Table(name="nmt_inventory_serial", uniqueConstraints={@ORM\UniqueConstraint(name="serial_number_UNIQUE", columns={"serial_number"})}, indexes={@ORM\Index(name="nmt_inventory_serial_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_serial_FK2_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_inventory_serial_FK3_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class NmtInventorySerial
{

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="serial_number", type="string", length=45, nullable=true)
     */
    private $serialNumber;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="consumed_on", type="datetime", nullable=true)
     */
    private $consumedOn;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="mfg_serial_number", type="string", length=45, nullable=true)
     */
    private $mfgSerialNumber;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_date", type="datetime", nullable=true)
     */
    private $mfgDate;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="lot_number", type="string", length=45, nullable=true)
     */
    private $lotNumber;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_warranty_start", type="datetime", nullable=true)
     */
    private $mfgWarrantyStart;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_warranty_end", type="datetime", nullable=true)
     */
    private $mfgWarrantyEnd;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="item_name", type="string", length=100, nullable=true)
     */
    private $itemName;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=45, nullable=true)
     */
    private $category;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="mfg_name", type="string", length=100, nullable=true)
     */
    private $mfgName;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     *
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     *
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;

    /**
     *
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

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
     * @return NmtInventorySerial
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
     * Set serialNumber
     *
     * @param string $serialNumber
     *
     * @return NmtInventorySerial
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventorySerial
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventorySerial
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
     * @return NmtInventorySerial
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
     * Set consumedOn
     *
     * @param \DateTime $consumedOn
     *
     * @return NmtInventorySerial
     */
    public function setConsumedOn($consumedOn)
    {
        $this->consumedOn = $consumedOn;

        return $this;
    }

    /**
     * Get consumedOn
     *
     * @return \DateTime
     */
    public function getConsumedOn()
    {
        return $this->consumedOn;
    }

    /**
     * Set mfgSerialNumber
     *
     * @param string $mfgSerialNumber
     *
     * @return NmtInventorySerial
     */
    public function setMfgSerialNumber($mfgSerialNumber)
    {
        $this->mfgSerialNumber = $mfgSerialNumber;

        return $this;
    }

    /**
     * Get mfgSerialNumber
     *
     * @return string
     */
    public function getMfgSerialNumber()
    {
        return $this->mfgSerialNumber;
    }

    /**
     * Set mfgDate
     *
     * @param \DateTime $mfgDate
     *
     * @return NmtInventorySerial
     */
    public function setMfgDate($mfgDate)
    {
        $this->mfgDate = $mfgDate;

        return $this;
    }

    /**
     * Get mfgDate
     *
     * @return \DateTime
     */
    public function getMfgDate()
    {
        return $this->mfgDate;
    }

    /**
     * Set lotNumber
     *
     * @param string $lotNumber
     *
     * @return NmtInventorySerial
     */
    public function setLotNumber($lotNumber)
    {
        $this->lotNumber = $lotNumber;

        return $this;
    }

    /**
     * Get lotNumber
     *
     * @return string
     */
    public function getLotNumber()
    {
        return $this->lotNumber;
    }

    /**
     * Set mfgWarrantyStart
     *
     * @param \DateTime $mfgWarrantyStart
     *
     * @return NmtInventorySerial
     */
    public function setMfgWarrantyStart($mfgWarrantyStart)
    {
        $this->mfgWarrantyStart = $mfgWarrantyStart;

        return $this;
    }

    /**
     * Get mfgWarrantyStart
     *
     * @return \DateTime
     */
    public function getMfgWarrantyStart()
    {
        return $this->mfgWarrantyStart;
    }

    /**
     * Set mfgWarrantyEnd
     *
     * @param \DateTime $mfgWarrantyEnd
     *
     * @return NmtInventorySerial
     */
    public function setMfgWarrantyEnd($mfgWarrantyEnd)
    {
        $this->mfgWarrantyEnd = $mfgWarrantyEnd;

        return $this;
    }

    /**
     * Get mfgWarrantyEnd
     *
     * @return \DateTime
     */
    public function getMfgWarrantyEnd()
    {
        return $this->mfgWarrantyEnd;
    }

    /**
     * Set itemName
     *
     * @param string $itemName
     *
     * @return NmtInventorySerial
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get itemName
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return NmtInventorySerial
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return NmtInventorySerial
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set mfgName
     *
     * @param string $mfgName
     *
     * @return NmtInventorySerial
     */
    public function setMfgName($mfgName)
    {
        $this->mfgName = $mfgName;

        return $this;
    }

    /**
     * Get mfgName
     *
     * @return string
     */
    public function getMfgName()
    {
        return $this->mfgName;
    }

    /**
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtInventorySerial
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventorySerial
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventorySerial
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
     * @return NmtInventorySerial
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventorySerial
     */
    public function setItem(\Application\Entity\NmtInventoryItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getItem()
    {
        return $this->item;
    }
}
