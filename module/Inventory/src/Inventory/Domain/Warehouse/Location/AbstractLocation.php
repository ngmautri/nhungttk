<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractLocation extends AbstractEntity implements AggregateRootInterface
{

    protected $id;

    protected $createdOn;

    protected $sysNumber;

    protected $token;

    protected $lastChangeOn;

    protected $revisionNo;

    protected $remarks;

    protected $isSystemLocation;

    protected $isReturnLocation;

    protected $isScrapLocation;

    protected $isRootLocation;

    protected $locationName;

    protected $locationCode;

    protected $parentId;

    protected $locationType;

    protected $isActive;

    protected $isLocked;

    protected $path;

    protected $pathDepth;

    protected $hasMember;

    protected $uuid;

    protected $createdBy;

    protected $lastChangeBy;

    protected $warehouse;

    protected $parentUuid;

    protected $parentCode;

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getIsSystemLocation()
    {
        return $this->isSystemLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReturnLocation()
    {
        return $this->isReturnLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getIsScrapLocation()
    {
        return $this->isScrapLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getIsRootLocation()
    {
        return $this->isRootLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     *
     * @return mixed
     */
    public function getLocationCode()
    {
        return $this->locationCode;
    }

    /**
     *
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     *
     * @return mixed
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @return mixed
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     *
     * @return mixed
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getParentUuid()
    {
        return $this->parentUuid;
    }

    /**
     *
     * @return mixed
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isSystemLocation
     */
    protected function setIsSystemLocation($isSystemLocation)
    {
        $this->isSystemLocation = $isSystemLocation;
    }

    /**
     *
     * @param mixed $isReturnLocation
     */
    protected function setIsReturnLocation($isReturnLocation)
    {
        $this->isReturnLocation = $isReturnLocation;
    }

    /**
     *
     * @param mixed $isScrapLocation
     */
    protected function setIsScrapLocation($isScrapLocation)
    {
        $this->isScrapLocation = $isScrapLocation;
    }

    /**
     *
     * @param mixed $isRootLocation
     */
    protected function setIsRootLocation($isRootLocation)
    {
        $this->isRootLocation = $isRootLocation;
    }

    /**
     *
     * @param mixed $locationName
     */
    protected function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     *
     * @param mixed $locationCode
     */
    protected function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
    }

    /**
     *
     * @param mixed $parentId
     */
    protected function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     *
     * @param mixed $locationType
     */
    protected function setLocationType($locationType)
    {
        $this->locationType = $locationType;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $isLocked
     */
    protected function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @param mixed $path
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     *
     * @param mixed $pathDepth
     */
    protected function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;
    }

    /**
     *
     * @param mixed $hasMember
     */
    protected function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $warehouse
     */
    protected function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     *
     * @param mixed $parentUuid
     */
    protected function setParentUuid($parentUuid)
    {
        $this->parentUuid = $parentUuid;
    }

    /**
     *
     * @param mixed $parentCode
     */
    protected function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
    }
}