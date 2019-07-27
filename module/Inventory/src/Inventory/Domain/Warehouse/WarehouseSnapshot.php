<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseSnapshot extends AbstractValueObject
{

    public $id;

    public $whCode;

    public $whName;

    public $whAddress;

    public $whContactPerson;

    public $whTelephone;

    public $whEmail;

    public $isLocked;

    public $whStatus;

    public $remarks;

    public $isDefault;

    public $createdOn;

    public $sysNumber;

    public $token;

    public $lastChangeOn;

    public $revisionNo;

    public $createdBy;

    public $company;

    public $whCountry;

    public $lastChangeBy;

    public $stockkeeper;

    public $whController;

    public $location;

    public $uuid;
}