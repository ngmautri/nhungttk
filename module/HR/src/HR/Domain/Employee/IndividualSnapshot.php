<?php
namespace HR\Domain\Employee;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualSnapshot extends AbstractDTO
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

    public $company;

    public $revisionNo;

    public $version;

    public $sysNumber;

    public $token;

    public $uuid;

    public $passportNo;

    public $passportIssuePlace;

    public $passportIssueDate;

    public $passportExpiredDate;

    public $workPermitNo;

    public $workPermitDate;

    public $workPermitExpiredDate;

    public $familyBookNo;

    public $ssoNumber;

    public $personalIdNumberDate;

    public $personalIdNumberExpiredDate;

    public $nationality;

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
    public function getIndividualType()
    {
        return $this->individualType;
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
     * @return mixed
     */
    public function getIndividualNameLocal()
    {
        return $this->individualNameLocal;
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
     * @return mixed
     */
    public function getFirstNameLocal()
    {
        return $this->firstNameLocal;
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
     * @return mixed
     */
    public function getMiddleNameLocal()
    {
        return $this->middleNameLocal;
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
     * @return mixed
     */
    public function getLastNameLocal()
    {
        return $this->lastNameLocal;
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
     * @return mixed
     */
    public function getPersonalIdNumber()
    {
        return $this->personalIdNumber;
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
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
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
    public function getCompany()
    {
        return $this->company;
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
    public function getVersion()
    {
        return $this->version;
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
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getPassportNo()
    {
        return $this->passportNo;
    }

    /**
     *
     * @return mixed
     */
    public function getPassportIssuePlace()
    {
        return $this->passportIssuePlace;
    }

    /**
     *
     * @return mixed
     */
    public function getPassportIssueDate()
    {
        return $this->passportIssueDate;
    }

    /**
     *
     * @return mixed
     */
    public function getPassportExpiredDate()
    {
        return $this->passportExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkPermitNo()
    {
        return $this->workPermitNo;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkPermitDate()
    {
        return $this->workPermitDate;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkPermitExpiredDate()
    {
        return $this->workPermitExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    public function getFamilyBookNo()
    {
        return $this->familyBookNo;
    }

    /**
     *
     * @return mixed
     */
    public function getSsoNumber()
    {
        return $this->ssoNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getPersonalIdNumberDate()
    {
        return $this->personalIdNumberDate;
    }

    /**
     *
     * @return mixed
     */
    public function getPersonalIdNumberExpiredDate()
    {
        return $this->personalIdNumberExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
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
     * @param mixed $individualType
     */
    public function setIndividualType($individualType)
    {
        $this->individualType = $individualType;
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
     * @param mixed $individualNameLocal
     */
    public function setIndividualNameLocal($individualNameLocal)
    {
        $this->individualNameLocal = $individualNameLocal;
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
     * @param mixed $firstNameLocal
     */
    public function setFirstNameLocal($firstNameLocal)
    {
        $this->firstNameLocal = $firstNameLocal;
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
     * @param mixed $middleNameLocal
     */
    public function setMiddleNameLocal($middleNameLocal)
    {
        $this->middleNameLocal = $middleNameLocal;
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
     * @param mixed $lastNameLocal
     */
    public function setLastNameLocal($lastNameLocal)
    {
        $this->lastNameLocal = $lastNameLocal;
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
     * @param mixed $personalIdNumber
     */
    public function setPersonalIdNumber($personalIdNumber)
    {
        $this->personalIdNumber = $personalIdNumber;
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
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
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
     * @param mixed $employeeCode
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
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
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
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
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $passportNo
     */
    public function setPassportNo($passportNo)
    {
        $this->passportNo = $passportNo;
    }

    /**
     *
     * @param mixed $passportIssuePlace
     */
    public function setPassportIssuePlace($passportIssuePlace)
    {
        $this->passportIssuePlace = $passportIssuePlace;
    }

    /**
     *
     * @param mixed $passportIssueDate
     */
    public function setPassportIssueDate($passportIssueDate)
    {
        $this->passportIssueDate = $passportIssueDate;
    }

    /**
     *
     * @param mixed $passportExpiredDate
     */
    public function setPassportExpiredDate($passportExpiredDate)
    {
        $this->passportExpiredDate = $passportExpiredDate;
    }

    /**
     *
     * @param mixed $workPermitNo
     */
    public function setWorkPermitNo($workPermitNo)
    {
        $this->workPermitNo = $workPermitNo;
    }

    /**
     *
     * @param mixed $workPermitDate
     */
    public function setWorkPermitDate($workPermitDate)
    {
        $this->workPermitDate = $workPermitDate;
    }

    /**
     *
     * @param mixed $workPermitExpiredDate
     */
    public function setWorkPermitExpiredDate($workPermitExpiredDate)
    {
        $this->workPermitExpiredDate = $workPermitExpiredDate;
    }

    /**
     *
     * @param mixed $familyBookNo
     */
    public function setFamilyBookNo($familyBookNo)
    {
        $this->familyBookNo = $familyBookNo;
    }

    /**
     *
     * @param mixed $ssoNumber
     */
    public function setSsoNumber($ssoNumber)
    {
        $this->ssoNumber = $ssoNumber;
    }

    /**
     *
     * @param mixed $personalIdNumberDate
     */
    public function setPersonalIdNumberDate($personalIdNumberDate)
    {
        $this->personalIdNumberDate = $personalIdNumberDate;
    }

    /**
     *
     * @param mixed $personalIdNumberExpiredDate
     */
    public function setPersonalIdNumberExpiredDate($personalIdNumberExpiredDate)
    {
        $this->personalIdNumberExpiredDate = $personalIdNumberExpiredDate;
    }

    /**
     *
     * @param mixed $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }
}