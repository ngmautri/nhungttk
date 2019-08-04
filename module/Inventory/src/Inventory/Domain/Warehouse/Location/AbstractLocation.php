<?php
namespace Inventory\Domain\Warehouse\Location;

use Inventory\Application\DTO\Warehouse\Location\LocationDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractLocation
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

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public function makeSnapshot()
    {
        return LocationSnapshotAssembler::createSnapshotFrom($this);
    }

    public function makeDTO()
    {
        return LocationDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param LocationSnapshot $snapshot
     */
    public function makeFromSnapshot(LocationSnapshot $snapshot)
    {
        if (! $snapshot instanceof LocationSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->createdOn = $snapshot->createdOn;
        $this->sysNumber = $snapshot->sysNumber;
        $this->token = $snapshot->token;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->revisionNo = $snapshot->revisionNo;
        $this->remarks = $snapshot->remarks;
        $this->isSystemLocation = $snapshot->isSystemLocation;
        $this->isReturnLocation = $snapshot->isReturnLocation;
        $this->isScrapLocation = $snapshot->isScrapLocation;
        $this->isRootLocation = $snapshot->isRootLocation;
        $this->locationName = $snapshot->locationName;
        $this->locationCode = $snapshot->locationCode;
        $this->parentId = $snapshot->parentId;
        $this->locationType = $snapshot->locationType;
        $this->isActive = $snapshot->isActive;
        $this->isLocked = $snapshot->isLocked;
        $this->path = $snapshot->path;
        $this->pathDepth = $snapshot->pathDepth;
        $this->hasMember = $snapshot->hasMember;
        $this->uuid = $snapshot->uuid;
        $this->createdBy = $snapshot->createdBy;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->warehouse = $snapshot->warehouse;
    }

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