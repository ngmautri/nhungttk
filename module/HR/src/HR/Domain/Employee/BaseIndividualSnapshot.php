<?php
namespace HR\Domain\Employee;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseIndividualSnapshot extends IndividualSnapshot
{

    public $id;

    public $individualType;

    public $individualName;

    public $individualNameLocal;

    public $firstName;

    public $firstNameLocal;

    public $middleName;

    public $middleNameLocal;

    public $lastName;

    public $lastNameLocal;

    public $nickName;

    public $personalIdNumber;

    public $gender;

    public $birthday;

    public $lastStatusId;

    public $createdOn;

    public $lastChangeOn;

    public $remarks;

    public $employeeStatus;

    public $employeeCode;

    public $createdBy;

    public $lastChangeBy;

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
    public function getIndividualType()
    {
        return $this->individualType;
    }

    /**
     *
     * @param mixed $individualType
     */
    public function setIndividualType($individualType)
    {
        $this->individualType = $individualType;
    }

    /**
     *
     * @return mixed
     */
    public function getIndividualName()
    {
        return $this->individualName;
    }

    /**
     *
     * @param mixed $individualName
     */
    public function setIndividualName($individualName)
    {
        $this->individualName = $individualName;
    }

    /**
     *
     * @return mixed
     */
    public function getIndividualNameLocal()
    {
        return $this->individualNameLocal;
    }

    /**
     *
     * @param mixed $individualNameLocal
     */
    public function setIndividualNameLocal($individualNameLocal)
    {
        $this->individualNameLocal = $individualNameLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @return mixed
     */
    public function getFirstNameLocal()
    {
        return $this->firstNameLocal;
    }

    /**
     *
     * @param mixed $firstNameLocal
     */
    public function setFirstNameLocal($firstNameLocal)
    {
        $this->firstNameLocal = $firstNameLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     *
     * @param mixed $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     *
     * @return mixed
     */
    public function getMiddleNameLocal()
    {
        return $this->middleNameLocal;
    }

    /**
     *
     * @param mixed $middleNameLocal
     */
    public function setMiddleNameLocal($middleNameLocal)
    {
        $this->middleNameLocal = $middleNameLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     *
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastNameLocal()
    {
        return $this->lastNameLocal;
    }

    /**
     *
     * @param mixed $lastNameLocal
     */
    public function setLastNameLocal($lastNameLocal)
    {
        $this->lastNameLocal = $lastNameLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     *
     * @param mixed $nickName
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     *
     * @return mixed
     */
    public function getPersonalIdNumber()
    {
        return $this->personalIdNumber;
    }

    /**
     *
     * @param mixed $personalIdNumber
     */
    public function setPersonalIdNumber($personalIdNumber)
    {
        $this->personalIdNumber = $personalIdNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     *
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     *
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     *
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     *
     * @return mixed
     */
    public function getLastStatusId()
    {
        return $this->lastStatusId;
    }

    /**
     *
     * @param mixed $lastStatusId
     */
    public function setLastStatusId($lastStatusId)
    {
        $this->lastStatusId = $lastStatusId;
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
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
    }

    /**
     *
     * @param mixed $employeeStatus
     */
    public function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
    }

    /**
     *
     * @param mixed $employeeCode
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
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
}