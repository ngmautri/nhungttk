<?php
namespace Inventory\Domain\Warehouse\Location;

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
   
}