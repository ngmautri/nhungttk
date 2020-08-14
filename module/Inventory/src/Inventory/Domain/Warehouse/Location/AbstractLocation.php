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
     * @param mixed $parentUuid
     */
    public function setParentUuid($parentUuid)
    {
        $this->parentUuid = $parentUuid;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isSystemLocation
     */
    public function setIsSystemLocation($isSystemLocation)
    {
        $this->isSystemLocation = $isSystemLocation;
    }

    /**
     *
     * @param mixed $isReturnLocation
     */
    public function setIsReturnLocation($isReturnLocation)
    {
        $this->isReturnLocation = $isReturnLocation;
    }

    /**
     *
     * @param mixed $isScrapLocation
     */
    public function setIsScrapLocation($isScrapLocation)
    {
        $this->isScrapLocation = $isScrapLocation;
    }

    /**
     *
     * @param mixed $isRootLocation
     */
    public function setIsRootLocation($isRootLocation)
    {
        $this->isRootLocation = $isRootLocation;
    }

    /**
     *
     * @param mixed $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
    }

    /**
     *
     * @param mixed $locationCode
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
    }

    /**
     *
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     *
     * @param mixed $locationType
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     *
     * @param mixed $pathDepth
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;
    }

    /**
     *
     * @param mixed $hasMember
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;
    }

    /**
     *
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }
}