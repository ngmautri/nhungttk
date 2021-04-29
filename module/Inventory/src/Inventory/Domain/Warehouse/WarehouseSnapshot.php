<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseSnapshot extends AbstractDTO
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
    public function getWhCode()
    {
        return $this->whCode;
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
     * @return mixed
     */
    public function getWhName()
    {
        return $this->whName;
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
     * @return mixed
     */
    public function getWhAddress()
    {
        return $this->whAddress;
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
     * @return mixed
     */
    public function getWhContactPerson()
    {
        return $this->whContactPerson;
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
     * @return mixed
     */
    public function getWhTelephone()
    {
        return $this->whTelephone;
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
     * @return mixed
     */
    public function getWhEmail()
    {
        return $this->whEmail;
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
    public function getWhStatus()
    {
        return $this->whStatus;
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
    public function getIsDefault()
    {
        return $this->isDefault;
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
    public function getCompany()
    {
        return $this->company;
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
     * @return mixed
     */
    public function getWhCountry()
    {
        return $this->whCountry;
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
    public function getStockkeeper()
    {
        return $this->stockkeeper;
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
     * @return mixed
     */
    public function getWhController()
    {
        return $this->whController;
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
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }
}