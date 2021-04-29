<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class LocationSnapshot extends AbstractDTO
{

    public $id;

    public $createdOn;

    public $sysNumber;

    public $token;

    public $lastChangeOn;

    public $revisionNo;

    public $remarks;

    public $isSystemLocation;

    public $isReturnLocation;

    public $isScrapLocation;

    public $isRootLocation;

    public $locationName;

    public $locationCode;

    public $parentId;

    public $locationType;

    public $isActive;

    public $isLocked;

    public $path;

    public $pathDepth;

    public $hasMember;

    public $uuid;

    public $createdBy;

    public $lastChangeBy;

    public $warehouse;

    public $parentUuid;

    public $parentCode;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
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
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
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
     * @param mixed $isSystemLocation
     */
    public function setIsSystemLocation($isSystemLocation)
    {
        $this->isSystemLocation = $isSystemLocation;
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
     * @param mixed $isReturnLocation
     */
    public function setIsReturnLocation($isReturnLocation)
    {
        $this->isReturnLocation = $isReturnLocation;
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
     * @param mixed $isScrapLocation
     */
    public function setIsScrapLocation($isScrapLocation)
    {
        $this->isScrapLocation = $isScrapLocation;
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
     * @param mixed $isRootLocation
     */
    public function setIsRootLocation($isRootLocation)
    {
        $this->isRootLocation = $isRootLocation;
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
     * @param mixed $locationName
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;
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
     * @param mixed $locationCode
     */
    public function setLocationCode($locationCode)
    {
        $this->locationCode = $locationCode;
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
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
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
     * @param mixed $locationType
     */
    public function setLocationType($locationType)
    {
        $this->locationType = $locationType;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $isLocked
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
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
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
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
     * @param mixed $pathDepth
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;
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
     * @param mixed $hasMember
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;
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
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
     * @param mixed $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
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
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     *
     * @param mixed $parentCode
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
    }
}