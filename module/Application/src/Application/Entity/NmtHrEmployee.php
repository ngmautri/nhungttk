<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrEmployee
 *
 * @ORM\Table(name="nmt_hr_employee", uniqueConstraints={@ORM\UniqueConstraint(name="employee_code_UNIQUE", columns={"employee_code"})}, indexes={@ORM\Index(name="nmt_hr_employee_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_employee_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_hr_employee_FK4_idx", columns={"birth_country"}), @ORM\Index(name="nmt_hr_employee_INX1", columns={"employee_code"}), @ORM\Index(name="nmt_hr_employee_FK3_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtHrEmployee
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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_code", type="string", length=10, nullable=false)
     */
    private $employeeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_name", type="string", length=80, nullable=false)
     */
    private $employeeName;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_name_local", type="string", length=80, nullable=true)
     */
    private $employeeNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

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
     * @ORM\Column(name="sso_number", type="string", length=45, nullable=true)
     */
    private $ssoNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sso_date", type="datetime", nullable=true)
     */
    private $ssoDate;

    /**
     * @var string
     *
     * @ORM\Column(name="family_book_no", type="string", length=45, nullable=true)
     */
    private $familyBookNo;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_id_no", type="string", length=45, nullable=true)
     */
    private $personalIdNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="personal_id_issue_date", type="datetime", nullable=true)
     */
    private $personalIdIssueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_id_issue_place", type="string", length=45, nullable=true)
     */
    private $personalIdIssuePlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="personal_id_expire_date", type="datetime", nullable=true)
     */
    private $personalIdExpireDate;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_no", type="string", length=45, nullable=true)
     */
    private $passportNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passport_issue_date", type="datetime", nullable=true)
     */
    private $passportIssueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="passport_issue_place", type="string", length=45, nullable=true)
     */
    private $passportIssuePlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passport_expire_date", type="datetime", nullable=true)
     */
    private $passportExpireDate;

    /**
     * @var string
     *
     * @ORM\Column(name="stay_permit_no", type="string", length=45, nullable=true)
     */
    private $stayPermitNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stay_permit_issue_date", type="datetime", nullable=true)
     */
    private $stayPermitIssueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="stay_permit_issue_place", type="string", length=45, nullable=true)
     */
    private $stayPermitIssuePlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stay_permit_expire_date", type="datetime", nullable=true)
     */
    private $stayPermitExpireDate;

    /**
     * @var string
     *
     * @ORM\Column(name="work_permit_no", type="string", length=45, nullable=true)
     */
    private $workPermitNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="work_permit_issue_date", type="datetime", nullable=true)
     */
    private $workPermitIssueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="work_permint_issue_place", type="string", length=45, nullable=true)
     */
    private $workPermintIssuePlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="work_permit_expire_date", type="datetime", nullable=true)
     */
    private $workPermitExpireDate;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_pmt_method", type="string", length=45, nullable=true)
     */
    private $salaryPmtMethod;

    /**
     * @var integer
     *
     * @ORM\Column(name="nationality", type="integer", nullable=false)
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

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
     *   @ORM\JoinColumn(name="birth_country", referencedColumnName="id")
     * })
     */
    private $birthCountry;



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
     * Set token
     *
     * @param string $token
     *
     * @return NmtHrEmployee
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
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtHrEmployee
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set employeeCode
     *
     * @param string $employeeCode
     *
     * @return NmtHrEmployee
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
     * Set employeeName
     *
     * @param string $employeeName
     *
     * @return NmtHrEmployee
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;

        return $this;
    }

    /**
     * Get employeeName
     *
     * @return string
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * Set employeeNameLocal
     *
     * @param string $employeeNameLocal
     *
     * @return NmtHrEmployee
     */
    public function setEmployeeNameLocal($employeeNameLocal)
    {
        $this->employeeNameLocal = $employeeNameLocal;

        return $this;
    }

    /**
     * Get employeeNameLocal
     *
     * @return string
     */
    public function getEmployeeNameLocal()
    {
        return $this->employeeNameLocal;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return NmtHrEmployee
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
     * @return NmtHrEmployee
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtHrEmployee
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrEmployee
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
     * @return NmtHrEmployee
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
     * Set ssoNumber
     *
     * @param string $ssoNumber
     *
     * @return NmtHrEmployee
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
     * Set ssoDate
     *
     * @param \DateTime $ssoDate
     *
     * @return NmtHrEmployee
     */
    public function setSsoDate($ssoDate)
    {
        $this->ssoDate = $ssoDate;

        return $this;
    }

    /**
     * Get ssoDate
     *
     * @return \DateTime
     */
    public function getSsoDate()
    {
        return $this->ssoDate;
    }

    /**
     * Set familyBookNo
     *
     * @param string $familyBookNo
     *
     * @return NmtHrEmployee
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
     * Set personalIdNo
     *
     * @param string $personalIdNo
     *
     * @return NmtHrEmployee
     */
    public function setPersonalIdNo($personalIdNo)
    {
        $this->personalIdNo = $personalIdNo;

        return $this;
    }

    /**
     * Get personalIdNo
     *
     * @return string
     */
    public function getPersonalIdNo()
    {
        return $this->personalIdNo;
    }

    /**
     * Set personalIdIssueDate
     *
     * @param \DateTime $personalIdIssueDate
     *
     * @return NmtHrEmployee
     */
    public function setPersonalIdIssueDate($personalIdIssueDate)
    {
        $this->personalIdIssueDate = $personalIdIssueDate;

        return $this;
    }

    /**
     * Get personalIdIssueDate
     *
     * @return \DateTime
     */
    public function getPersonalIdIssueDate()
    {
        return $this->personalIdIssueDate;
    }

    /**
     * Set personalIdIssuePlace
     *
     * @param string $personalIdIssuePlace
     *
     * @return NmtHrEmployee
     */
    public function setPersonalIdIssuePlace($personalIdIssuePlace)
    {
        $this->personalIdIssuePlace = $personalIdIssuePlace;

        return $this;
    }

    /**
     * Get personalIdIssuePlace
     *
     * @return string
     */
    public function getPersonalIdIssuePlace()
    {
        return $this->personalIdIssuePlace;
    }

    /**
     * Set personalIdExpireDate
     *
     * @param \DateTime $personalIdExpireDate
     *
     * @return NmtHrEmployee
     */
    public function setPersonalIdExpireDate($personalIdExpireDate)
    {
        $this->personalIdExpireDate = $personalIdExpireDate;

        return $this;
    }

    /**
     * Get personalIdExpireDate
     *
     * @return \DateTime
     */
    public function getPersonalIdExpireDate()
    {
        return $this->personalIdExpireDate;
    }

    /**
     * Set passportNo
     *
     * @param string $passportNo
     *
     * @return NmtHrEmployee
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
     * Set passportIssueDate
     *
     * @param \DateTime $passportIssueDate
     *
     * @return NmtHrEmployee
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
     * Set passportIssuePlace
     *
     * @param string $passportIssuePlace
     *
     * @return NmtHrEmployee
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
     * Set passportExpireDate
     *
     * @param \DateTime $passportExpireDate
     *
     * @return NmtHrEmployee
     */
    public function setPassportExpireDate($passportExpireDate)
    {
        $this->passportExpireDate = $passportExpireDate;

        return $this;
    }

    /**
     * Get passportExpireDate
     *
     * @return \DateTime
     */
    public function getPassportExpireDate()
    {
        return $this->passportExpireDate;
    }

    /**
     * Set stayPermitNo
     *
     * @param string $stayPermitNo
     *
     * @return NmtHrEmployee
     */
    public function setStayPermitNo($stayPermitNo)
    {
        $this->stayPermitNo = $stayPermitNo;

        return $this;
    }

    /**
     * Get stayPermitNo
     *
     * @return string
     */
    public function getStayPermitNo()
    {
        return $this->stayPermitNo;
    }

    /**
     * Set stayPermitIssueDate
     *
     * @param \DateTime $stayPermitIssueDate
     *
     * @return NmtHrEmployee
     */
    public function setStayPermitIssueDate($stayPermitIssueDate)
    {
        $this->stayPermitIssueDate = $stayPermitIssueDate;

        return $this;
    }

    /**
     * Get stayPermitIssueDate
     *
     * @return \DateTime
     */
    public function getStayPermitIssueDate()
    {
        return $this->stayPermitIssueDate;
    }

    /**
     * Set stayPermitIssuePlace
     *
     * @param string $stayPermitIssuePlace
     *
     * @return NmtHrEmployee
     */
    public function setStayPermitIssuePlace($stayPermitIssuePlace)
    {
        $this->stayPermitIssuePlace = $stayPermitIssuePlace;

        return $this;
    }

    /**
     * Get stayPermitIssuePlace
     *
     * @return string
     */
    public function getStayPermitIssuePlace()
    {
        return $this->stayPermitIssuePlace;
    }

    /**
     * Set stayPermitExpireDate
     *
     * @param \DateTime $stayPermitExpireDate
     *
     * @return NmtHrEmployee
     */
    public function setStayPermitExpireDate($stayPermitExpireDate)
    {
        $this->stayPermitExpireDate = $stayPermitExpireDate;

        return $this;
    }

    /**
     * Get stayPermitExpireDate
     *
     * @return \DateTime
     */
    public function getStayPermitExpireDate()
    {
        return $this->stayPermitExpireDate;
    }

    /**
     * Set workPermitNo
     *
     * @param string $workPermitNo
     *
     * @return NmtHrEmployee
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
     * Set workPermitIssueDate
     *
     * @param \DateTime $workPermitIssueDate
     *
     * @return NmtHrEmployee
     */
    public function setWorkPermitIssueDate($workPermitIssueDate)
    {
        $this->workPermitIssueDate = $workPermitIssueDate;

        return $this;
    }

    /**
     * Get workPermitIssueDate
     *
     * @return \DateTime
     */
    public function getWorkPermitIssueDate()
    {
        return $this->workPermitIssueDate;
    }

    /**
     * Set workPermintIssuePlace
     *
     * @param string $workPermintIssuePlace
     *
     * @return NmtHrEmployee
     */
    public function setWorkPermintIssuePlace($workPermintIssuePlace)
    {
        $this->workPermintIssuePlace = $workPermintIssuePlace;

        return $this;
    }

    /**
     * Get workPermintIssuePlace
     *
     * @return string
     */
    public function getWorkPermintIssuePlace()
    {
        return $this->workPermintIssuePlace;
    }

    /**
     * Set workPermitExpireDate
     *
     * @param \DateTime $workPermitExpireDate
     *
     * @return NmtHrEmployee
     */
    public function setWorkPermitExpireDate($workPermitExpireDate)
    {
        $this->workPermitExpireDate = $workPermitExpireDate;

        return $this;
    }

    /**
     * Get workPermitExpireDate
     *
     * @return \DateTime
     */
    public function getWorkPermitExpireDate()
    {
        return $this->workPermitExpireDate;
    }

    /**
     * Set salaryPmtMethod
     *
     * @param string $salaryPmtMethod
     *
     * @return NmtHrEmployee
     */
    public function setSalaryPmtMethod($salaryPmtMethod)
    {
        $this->salaryPmtMethod = $salaryPmtMethod;

        return $this;
    }

    /**
     * Get salaryPmtMethod
     *
     * @return string
     */
    public function getSalaryPmtMethod()
    {
        return $this->salaryPmtMethod;
    }

    /**
     * Set nationality
     *
     * @param integer $nationality
     *
     * @return NmtHrEmployee
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return integer
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtHrEmployee
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtHrEmployee
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
     * @return NmtHrEmployee
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
     * @return NmtHrEmployee
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
     * Set birthCountry
     *
     * @param \Application\Entity\NmtApplicationCountry $birthCountry
     *
     * @return NmtHrEmployee
     */
    public function setBirthCountry(\Application\Entity\NmtApplicationCountry $birthCountry = null)
    {
        $this->birthCountry = $birthCountry;

        return $this;
    }

    /**
     * Get birthCountry
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getBirthCountry()
    {
        return $this->birthCountry;
    }
}
