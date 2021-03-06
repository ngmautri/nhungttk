<?php
namespace HR\Domain\Employee;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 * Mapping with hr_indvidual
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractIndividual extends AbstractEntity implements AggregateRootInterface
{

    protected $id;

    protected $individualType;

    protected $individualName;

    protected $individualNameLocal;

    protected $firstName;

    protected $firstNameLocal;

    protected $middleName;

    protected $middleNameLocal;

    protected $lastName;

    protected $lastNameLocal;

    protected $nickName;

    protected $personalIdNumber;

    protected $gender;

    protected $birthday;

    protected $lastStatusId;

    protected $createdOn;

    protected $lastChangeOn;

    protected $remarks;

    protected $employeeStatus;

    protected $employeeCode;

    protected $createdBy;

    protected $lastChangeBy;

    protected $company;

    protected $revisionNo;

    protected $version;

    protected $sysNumber;

    protected $token;

    protected $uuid;

    protected $passportNo;

    protected $passportIssuePlace;

    protected $passportIssueDate;

    protected $passportExpiredDate;

    protected $workPermitNo;

    protected $workPermitDate;

    protected $workPermitExpiredDate;

    protected $familyBookNo;

    protected $ssoNumber;

    protected $personalIdNumberDate;

    protected $personalIdNumberExpiredDate;

    protected $nationality;

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
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $individualType
     */
    protected function setIndividualType($individualType)
    {
        $this->individualType = $individualType;
    }

    /**
     *
     * @param mixed $individualName
     */
    protected function setIndividualName($individualName)
    {
        $this->individualName = $individualName;
    }

    /**
     *
     * @param mixed $individualNameLocal
     */
    protected function setIndividualNameLocal($individualNameLocal)
    {
        $this->individualNameLocal = $individualNameLocal;
    }

    /**
     *
     * @param mixed $firstName
     */
    protected function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     *
     * @param mixed $firstNameLocal
     */
    protected function setFirstNameLocal($firstNameLocal)
    {
        $this->firstNameLocal = $firstNameLocal;
    }

    /**
     *
     * @param mixed $middleName
     */
    protected function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     *
     * @param mixed $middleNameLocal
     */
    protected function setMiddleNameLocal($middleNameLocal)
    {
        $this->middleNameLocal = $middleNameLocal;
    }

    /**
     *
     * @param mixed $lastName
     */
    protected function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     *
     * @param mixed $lastNameLocal
     */
    protected function setLastNameLocal($lastNameLocal)
    {
        $this->lastNameLocal = $lastNameLocal;
    }

    /**
     *
     * @param mixed $nickName
     */
    protected function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    /**
     *
     * @param mixed $personalIdNumber
     */
    protected function setPersonalIdNumber($personalIdNumber)
    {
        $this->personalIdNumber = $personalIdNumber;
    }

    /**
     *
     * @param mixed $gender
     */
    protected function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     *
     * @param mixed $birthday
     */
    protected function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     *
     * @param mixed $lastStatusId
     */
    protected function setLastStatusId($lastStatusId)
    {
        $this->lastStatusId = $lastStatusId;
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
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $employeeStatus
     */
    protected function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;
    }

    /**
     *
     * @param mixed $employeeCode
     */
    protected function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
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
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
    protected function setCompany($company)
    {
        $this->company = $company;
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
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $version
     */
    protected function setVersion($version)
    {
        $this->version = $version;
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
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
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

    /**
     *
     * @return mixed
     */
    protected function getPassportNo()
    {
        return $this->passportNo;
    }

    /**
     *
     * @return mixed
     */
    protected function getPassportIssuePlace()
    {
        return $this->passportIssuePlace;
    }

    /**
     *
     * @return mixed
     */
    protected function getPassportIssueDate()
    {
        return $this->passportIssueDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getPassportExpiredDate()
    {
        return $this->passportExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getWorkPermitNo()
    {
        return $this->workPermitNo;
    }

    /**
     *
     * @return mixed
     */
    protected function getWorkPermitDate()
    {
        return $this->workPermitDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getWorkPermitExpiredDate()
    {
        return $this->workPermitExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getFamilyBookNo()
    {
        return $this->familyBookNo;
    }

    /**
     *
     * @return mixed
     */
    protected function getSsoNumber()
    {
        return $this->ssoNumber;
    }

    /**
     *
     * @return mixed
     */
    protected function getPersonalIdNumberDate()
    {
        return $this->personalIdNumberDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getPersonalIdNumberExpiredDate()
    {
        return $this->personalIdNumberExpiredDate;
    }

    /**
     *
     * @return mixed
     */
    protected function getNationality()
    {
        return $this->nationality;
    }
}