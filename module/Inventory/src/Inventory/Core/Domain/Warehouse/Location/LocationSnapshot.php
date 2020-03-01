<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationSnapshot extends AbstractValueObject
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