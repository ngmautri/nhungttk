<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseWarehouseSnapshot extends AbstractDTO
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
     * @param mixed $locationList
     */
    public function setLocationList($locationList)
    {
        $this->locationList = $locationList;
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
     * @param mixed $whCode
     */
    public function setWhCode($whCode)
    {
        $this->whCode = $whCode;
    }

    /**
     *
     * @param mixed $whName
     */
    public function setWhName($whName)
    {
        $this->whName = $whName;
    }

    /**
     *
     * @param mixed $whAddress
     */
    public function setWhAddress($whAddress)
    {
        $this->whAddress = $whAddress;
    }

    /**
     *
     * @param mixed $whContactPerson
     */
    public function setWhContactPerson($whContactPerson)
    {
        $this->whContactPerson = $whContactPerson;
    }

    /**
     *
     * @param mixed $whTelephone
     */
    public function setWhTelephone($whTelephone)
    {
        $this->whTelephone = $whTelephone;
    }

    /**
     *
     * @param mixed $whEmail
     */
    public function setWhEmail($whEmail)
    {
        $this->whEmail = $whEmail;
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
     * @param mixed $whStatus
     */
    public function setWhStatus($whStatus)
    {
        $this->whStatus = $whStatus;
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
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
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
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     *
     * @param mixed $whCountry
     */
    public function setWhCountry($whCountry)
    {
        $this->whCountry = $whCountry;
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
     * @param mixed $stockkeeper
     */
    public function setStockkeeper($stockkeeper)
    {
        $this->stockkeeper = $stockkeeper;
    }

    /**
     *
     * @param mixed $whController
     */
    public function setWhController($whController)
    {
        $this->whController = $whController;
    }

    /**
     *
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

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