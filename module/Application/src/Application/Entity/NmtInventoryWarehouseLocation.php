<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWarehouseLocation
 *
 * @ORM\Table(name="nmt_inventory_warehouse_location", indexes={@ORM\Index(name="nmt_inventory_warehouse_location_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_warehouse_location_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_warehouse_location_FK3_idx", columns={"warehouse_id"})})
 * @ORM\Entity
 */
class NmtInventoryWarehouseLocation
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
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_system_location", type="boolean", nullable=true)
     */
    private $isSystemLocation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_return_location", type="boolean", nullable=true)
     */
    private $isReturnLocation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_scrap_location", type="boolean", nullable=true)
     */
    private $isScrapLocation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_root_location", type="boolean", nullable=true)
     */
    private $isRootLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="location_name", type="string", length=45, nullable=true)
     */
    private $locationName;

    /**
     * @var string
     *
     * @ORM\Column(name="location_code", type="string", length=45, nullable=true)
     */
    private $locationCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     *
     * @ORM\Column(name="location_type", type="string", length=45, nullable=true)
     */
    private $locationType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="path_depth", type="integer", nullable=true)
     */
    private $pathDepth;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_member", type="boolean", nullable=true)
     */
    private $hasMember;

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
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     * })
     */
    private $warehouse;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryWarehouseLocation
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
     * Set isSystemLocation
     *
     * @param boolean $isSystemLocation
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setIsSystemLocation($isSystemLocation)
    {
        $this->isSystemLocation = $isSystemLocation;

        return $this;
    }

    /**
     * Get isSystemLocation
     *
     * @return boolean
     */
    public function getIsSystemLocation()
    {
        return $this->isSystemLocation;
    }

    /**
     * Set isReturnLocation
     *
     * @param boolean $isReturnLocation
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setIsReturnLocation($isReturnLocation)
    {
        $this->isReturnLocation = $isReturnLocation;

        return $this;
    }

    /**
     * Get isReturnLocation
     *
     * @return boolean
     */
    public function getIsReturnLocation()
    {
        return $this->isReturnLocation;
    }

    /**
     * Set isScrapLocation
     *
     * @param boolean $isScrapLocation
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setIsScrapLocation($isScrapLocation)
    {
        $this->isScrapLocation = $isScrapLocation;

        return $this;
    }

    /**
     * Get isScrapLocation
     *
     * @return boolean
     */
    public function getIsScrapLocation()
    {
        return $this->isScrapLocation;
    }

    /**
     * Set isRootLocation
     *
     * @param boolean $isRootLocation
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setIsRootLocation($isRootLocation)
    {
        $this->isRootLocation = $isRootLocation;

        return $this;
    }

    /**
     * Get isRootLocation
     *
     * @return boolean
     */
    public function getIsRootLocation()
    {
        return $this->isRootLocation;
    }

    /**
     * Set locationName
     *
     * @param string $locationName
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;

        return $this;
    }

    /**
     * Get locationName
     *
     * @return string
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * Set locationCode
     *
     * @param string $locationCode
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;

        return $this;
    }

    /**
     * Get locationCode
     *
     * @return string
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set locationType
     *
     * @param string $locationType
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;

        return $this;
    }

    /**
     * Get locationType
     *
     * @return string
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryWarehouseLocation
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
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return NmtInventoryWarehouseLocation
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
     * Set path
     *
     * @param string $path
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set pathDepth
     *
     * @param integer $pathDepth
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;

        return $this;
    }

    /**
     * Get pathDepth
     *
     * @return integer
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     * Set hasMember
     *
     * @param boolean $hasMember
     *
     * @return NmtInventoryWarehouseLocation
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;

        return $this;
    }

    /**
     * Get hasMember
     *
     * @return boolean
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * @return NmtInventoryWarehouseLocation
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
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtInventoryWarehouseLocation
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
}
