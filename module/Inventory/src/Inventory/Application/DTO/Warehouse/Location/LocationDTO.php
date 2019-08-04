<?php
namespace Inventory\Application\DTO\Warehouse\Location;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationDTO
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
}
