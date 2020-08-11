<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseWarehouseSnapshot extends AbstractValueObject
{

    public $locationList;

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

    public $uuid;

    public $createdBy;

    public $company;

    public $whCountry;

    public $lastChangeBy;

    public $stockkeeper;

    public $whController;

    public $location;

    /**
     *
     * @return mixed
     */
    public function getLocationList()
    {
        return $this->locationList;
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
    public function getWhCode()
    {
        return $this->whCode;
    }

    /**
     *
     * @return mixed
     */
    public function getWhName()
    {
        return $this->whName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhAddress()
    {
        return $this->whAddress;
    }

    /**
     *
     * @return mixed
     */
    public function getWhContactPerson()
    {
        return $this->whContactPerson;
    }

    /**
     *
     * @return mixed
     */
    public function getWhTelephone()
    {
        return $this->whTelephone;
    }

    /**
     *
     * @return mixed
     */
    public function getWhEmail()
    {
        return $this->whEmail;
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
    public function getWhStatus()
    {
        return $this->whStatus;
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
    public function getIsDefault()
    {
        return $this->isDefault;
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @return mixed
     */
    public function getWhCountry()
    {
        return $this->whCountry;
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
    public function getStockkeeper()
    {
        return $this->stockkeeper;
    }

    /**
     *
     * @return mixed
     */
    public function getWhController()
    {
        return $this->whController;
    }

    /**
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }
}