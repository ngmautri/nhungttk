<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseLocationSnapshot extends AbstractValueObject
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
}