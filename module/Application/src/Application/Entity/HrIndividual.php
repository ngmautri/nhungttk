<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrIndividual
 *
 * @ORM\Table(name="hr_individual", indexes={@ORM\Index(name="hr_employee_FK1_idx", columns={"created_by"}), @ORM\Index(name="hr_individual_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="hr_individual_FK3_idx", columns={"company_id"}), @ORM\Index(name="hr_individual_FK4_idx", columns={"nationality"})})
 * @ORM\Entity
 */
class HrIndividual
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="individual_type", type="integer", nullable=false)
     */
    private $individualType;

    /**
     * @var string
     *
     * @ORM\Column(name="individual_name", type="string", length=150, nullable=false)
     */
    private $individualName;

    /**
     * @var string
     *
     * @ORM\Column(name="individual_name_local", type="string", length=150, nullable=true)
     */
    private $individualNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=150, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name_local", type="string", length=45, nullable=true)
     */
    private $firstNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=150, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name_local", type="string", length=150, nullable=true)
     */
    private $middleNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=150, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name_local", type="string", length=150, nullable=true)
     */
    private $lastNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="nick_name", type="string", length=45, nullable=true)
     */
    private $nickName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=false)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=false)
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_status_id", type="integer", nullable=true)
     */
    private $lastStatusId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="employee_status", type="integer", nullable=true)
     */
    private $employeeStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_code", type="string", length=20, nullable=true)
     */
    private $employeeCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=38, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=38, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_no", type="string", length=45, nullable=true)
     */
    private $passportNo;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_issue_place", type="string", length=45, nullable=true)
     */
    private $passportIssuePlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passport_issue_date", type="datetime", nullable=true)
     */
    private $passportIssueDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passport_expired_date", type="datetime", nullable=true)
     */
    private $passportExpiredDate;

    /**
     * @var string
     *
     * @ORM\Column(name="work_permit_no", type="string", length=45, nullable=true)
     */
    private $workPermitNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="work_permit_date", type="datetime", nullable=true)
     */
    private $workPermitDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="work_permit_expired_date", type="datetime", nullable=true)
     */
    private $workPermitExpiredDate;

    /**
     * @var string
     *
     * @ORM\Column(name="family_book_no", type="string", length=45, nullable=true)
     */
    private $familyBookNo;

    /**
     * @var string
     *
     * @ORM\Column(name="sso_number", type="string", length=45, nullable=true)
     */
    private $ssoNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_id_number", type="string", length=45, nullable=true)
     */
    private $personalIdNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="personal_id_number_date", type="datetime", nullable=true)
     */
    private $personalIdNumberDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="personal_id_number_expired_date", type="datetime", nullable=true)
     */
    private $personalIdNumberExpiredDate;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var \Application\Entity\NmtApplicationCountry
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCountry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nationality", referencedColumnName="id")
     * })
     */
    private $nationality;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set individualType
     *
     * @param integer $individualType
     *
     * @return HrIndividual
     */
    public function setIndividualType($individualType)
    {
        $this->individualType = $individualType;

        return $this;
    }

    /**
     * Get individualType
     *
     * @return integer
     */
    public function getIndividualType()
    {
        return $this->individualType;
    }

    /**
     * Set individualName
     *
     * @param string $individualName
     *
     * @return HrIndividual
     */
    public function setIndividualName($individualName)
    {
        $this->individualName = $individualName;

        return $this;
    }

    /**
     * Get individualName
     *
     * @return string
     */
    public function getIndividualName()
    {
        return $this->individualName;
    }

    /**
     * Set individualNameLocal
     *
     * @param string $individualNameLocal
     *
     * @return HrIndividual
     */
    public function setIndividualNameLocal($individualNameLocal)
    {
        $this->individualNameLocal = $individualNameLocal;

        return $this;
    }

    /**
     * Get individualNameLocal
     *
     * @return string
     */
    public function getIndividualNameLocal()
    {
        return $this->individualNameLocal;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return HrIndividual
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstNameLocal
     *
     * @param string $firstNameLocal
     *
     * @return HrIndividual
     */
    public function setFirstNameLocal($firstNameLocal)
    {
        $this->firstNameLocal = $firstNameLocal;

        return $this;
    }

    /**
     * Get firstNameLocal
     *
     * @return string
     */
    public function getFirstNameLocal()
    {
        return $this->firstNameLocal;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return HrIndividual
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set middleNameLocal
     *
     * @param string $middleNameLocal
     *
     * @return HrIndividual
     */
    public function setMiddleNameLocal($middleNameLocal)
    {
        $this->middleNameLocal = $middleNameLocal;

        return $this;
    }

    /**
     * Get middleNameLocal
     *
     * @return string
     */
    public function getMiddleNameLocal()
    {
        return $this->middleNameLocal;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return HrIndividual
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastNameLocal
     *
     * @param string $lastNameLocal
     *
     * @return HrIndividual
     */
    public function setLastNameLocal($lastNameLocal)
    {
        $this->lastNameLocal = $lastNameLocal;

        return $this;
    }

    /**
     * Get lastNameLocal
     *
     * @return string
     */
    public function getLastNameLocal()
    {
        return $this->lastNameLocal;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     *
     * @return HrIndividual
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return HrIndividual
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return HrIndividual
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set lastStatusId
     *
     * @param integer $lastStatusId
     *
     * @return HrIndividual
     */
    public function setLastStatusId($lastStatusId)
    {
        $this->lastStatusId = $lastStatusId;

        return $this;
    }

    /**
     * Get lastStatusId
     *
     * @return integer
     */
    public function getLastStatusId()
    {
        return $this->lastStatusId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return HrIndividual
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return HrIndividual
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrIndividual
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set employeeStatus
     *
     * @param integer $employeeStatus
     *
     * @return HrIndividual
     */
    public function setEmployeeStatus($employeeStatus)
    {
        $this->employeeStatus = $employeeStatus;

        return $this;
    }

    /**
     * Get employeeStatus
     *
     * @return integer
     */
    public function getEmployeeStatus()
    {
        return $this->employeeStatus;
    }

    /**
     * Set employeeCode
     *
     * @param string $employeeCode
     *
     * @return HrIndividual
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;

        return $this;
    }

    /**
     * Get employeeCode
     *
     * @return string
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return HrIndividual
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set version
     *
     * @param integer $version
     *
     * @return HrIndividual
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return HrIndividual
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;

        return $this;
    }

    /**
     * Get sysNumber
     *
     * @return string
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return HrIndividual
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return HrIndividual
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set passportNo
     *
     * @param string $passportNo
     *
     * @return HrIndividual
     */
    public function setPassportNo($passportNo)
    {
        $this->passportNo = $passportNo;

        return $this;
    }

    /**
     * Get passportNo
     *
     * @return string
     */
    public function getPassportNo()
    {
        return $this->passportNo;
    }

    /**
     * Set passportIssuePlace
     *
     * @param string $passportIssuePlace
     *
     * @return HrIndividual
     */
    public function setPassportIssuePlace($passportIssuePlace)
    {
        $this->passportIssuePlace = $passportIssuePlace;

        return $this;
    }

    /**
     * Get passportIssuePlace
     *
     * @return string
     */
    public function getPassportIssuePlace()
    {
        return $this->passportIssuePlace;
    }

    /**
     * Set passportIssueDate
     *
     * @param \DateTime $passportIssueDate
     *
     * @return HrIndividual
     */
    public function setPassportIssueDate($passportIssueDate)
    {
        $this->passportIssueDate = $passportIssueDate;

        return $this;
    }

    /**
     * Get passportIssueDate
     *
     * @return \DateTime
     */
    public function getPassportIssueDate()
    {
        return $this->passportIssueDate;
    }

    /**
     * Set passportExpiredDate
     *
     * @param \DateTime $passportExpiredDate
     *
     * @return HrIndividual
     */
    public function setPassportExpiredDate($passportExpiredDate)
    {
        $this->passportExpiredDate = $passportExpiredDate;

        return $this;
    }

    /**
     * Get passportExpiredDate
     *
     * @return \DateTime
     */
    public function getPassportExpiredDate()
    {
        return $this->passportExpiredDate;
    }

    /**
     * Set workPermitNo
     *
     * @param string $workPermitNo
     *
     * @return HrIndividual
     */
    public function setWorkPermitNo($workPermitNo)
    {
        $this->workPermitNo = $workPermitNo;

        return $this;
    }

    /**
     * Get workPermitNo
     *
     * @return string
     */
    public function getWorkPermitNo()
    {
        return $this->workPermitNo;
    }

    /**
     * Set workPermitDate
     *
     * @param \DateTime $workPermitDate
     *
     * @return HrIndividual
     */
    public function setWorkPermitDate($workPermitDate)
    {
        $this->workPermitDate = $workPermitDate;

        return $this;
    }

    /**
     * Get workPermitDate
     *
     * @return \DateTime
     */
    public function getWorkPermitDate()
    {
        return $this->workPermitDate;
    }

    /**
     * Set workPermitExpiredDate
     *
     * @param \DateTime $workPermitExpiredDate
     *
     * @return HrIndividual
     */
    public function setWorkPermitExpiredDate($workPermitExpiredDate)
    {
        $this->workPermitExpiredDate = $workPermitExpiredDate;

        return $this;
    }

    /**
     * Get workPermitExpiredDate
     *
     * @return \DateTime
     */
    public function getWorkPermitExpiredDate()
    {
        return $this->workPermitExpiredDate;
    }

    /**
     * Set familyBookNo
     *
     * @param string $familyBookNo
     *
     * @return HrIndividual
     */
    public function setFamilyBookNo($familyBookNo)
    {
        $this->familyBookNo = $familyBookNo;

        return $this;
    }

    /**
     * Get familyBookNo
     *
     * @return string
     */
    public function getFamilyBookNo()
    {
        return $this->familyBookNo;
    }

    /**
     * Set ssoNumber
     *
     * @param string $ssoNumber
     *
     * @return HrIndividual
     */
    public function setSsoNumber($ssoNumber)
    {
        $this->ssoNumber = $ssoNumber;

        return $this;
    }

    /**
     * Get ssoNumber
     *
     * @return string
     */
    public function getSsoNumber()
    {
        return $this->ssoNumber;
    }

    /**
     * Set personalIdNumber
     *
     * @param string $personalIdNumber
     *
     * @return HrIndividual
     */
    public function setPersonalIdNumber($personalIdNumber)
    {
        $this->personalIdNumber = $personalIdNumber;

        return $this;
    }

    /**
     * Get personalIdNumber
     *
     * @return string
     */
    public function getPersonalIdNumber()
    {
        return $this->personalIdNumber;
    }

    /**
     * Set personalIdNumberDate
     *
     * @param \DateTime $personalIdNumberDate
     *
     * @return HrIndividual
     */
    public function setPersonalIdNumberDate($personalIdNumberDate)
    {
        $this->personalIdNumberDate = $personalIdNumberDate;

        return $this;
    }

    /**
     * Get personalIdNumberDate
     *
     * @return \DateTime
     */
    public function getPersonalIdNumberDate()
    {
        return $this->personalIdNumberDate;
    }

    /**
     * Set personalIdNumberExpiredDate
     *
     * @param \DateTime $personalIdNumberExpiredDate
     *
     * @return HrIndividual
     */
    public function setPersonalIdNumberExpiredDate($personalIdNumberExpiredDate)
    {
        $this->personalIdNumberExpiredDate = $personalIdNumberExpiredDate;

        return $this;
    }

    /**
     * Get personalIdNumberExpiredDate
     *
     * @return \DateTime
     */
    public function getPersonalIdNumberExpiredDate()
    {
        return $this->personalIdNumberExpiredDate;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return HrIndividual
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return HrIndividual
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return HrIndividual
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set nationality
     *
     * @param \Application\Entity\NmtApplicationCountry $nationality
     *
     * @return HrIndividual
     */
    public function setNationality(\Application\Entity\NmtApplicationCountry $nationality = null)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getNationality()
    {
        return $this->nationality;
    }
}
