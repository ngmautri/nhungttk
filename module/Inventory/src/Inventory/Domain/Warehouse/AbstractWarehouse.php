<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractWarehouse extends AbstractEntity implements AggregateRootInterface
{

    protected $id;

    protected $whCode;

    protected $whName;

    protected $whAddress;

    protected $whContactPerson;

    protected $whTelephone;

    protected $whEmail;

    protected $isLocked;

    protected $whStatus;

    protected $remarks;

    protected $isDefault;

    protected $createdOn;

    protected $sysNumber;

    protected $token;

    protected $lastChangeOn;

    protected $revisionNo;

    protected $uuid;

    protected $createdBy;

    protected $company;

    protected $whCountry;

    protected $lastChangeBy;

    protected $stockkeeper;

    protected $whController;

    protected $location;

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

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $whCode
     */
    protected function setWhCode($whCode)
    {
        $this->whCode = $whCode;
    }

    /**
     *
     * @param mixed $whName
     */
    protected function setWhName($whName)
    {
        $this->whName = $whName;
    }

    /**
     *
     * @param mixed $whAddress
     */
    protected function setWhAddress($whAddress)
    {
        $this->whAddress = $whAddress;
    }

    /**
     *
     * @param mixed $whContactPerson
     */
    protected function setWhContactPerson($whContactPerson)
    {
        $this->whContactPerson = $whContactPerson;
    }

    /**
     *
     * @param mixed $whTelephone
     */
    protected function setWhTelephone($whTelephone)
    {
        $this->whTelephone = $whTelephone;
    }

    /**
     *
     * @param mixed $whEmail
     */
    protected function setWhEmail($whEmail)
    {
        $this->whEmail = $whEmail;
    }

    /**
     *
     * @param mixed $isLocked
     */
    protected function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;
    }

    /**
     *
     * @param mixed $whStatus
     */
    protected function setWhStatus($whStatus)
    {
        $this->whStatus = $whStatus;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $isDefault
     */
    protected function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     *
     * @param mixed $whCountry
     */
    protected function setWhCountry($whCountry)
    {
        $this->whCountry = $whCountry;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $stockkeeper
     */
    protected function setStockkeeper($stockkeeper)
    {
        $this->stockkeeper = $stockkeeper;
    }

    /**
     *
     * @param mixed $whController
     */
    protected function setWhController($whController)
    {
        $this->whController = $whController;
    }

    /**
     *
     * @param mixed $location
     */
    protected function setLocation($location)
    {
        $this->location = $location;
    }
}