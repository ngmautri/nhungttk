<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrIndividual
 *
 * @ORM\Table(name="hr_individual", indexes={@ORM\Index(name="hr_employee_FK1_idx", columns={"created_by"}), @ORM\Index(name="hr_individual_FK2_idx", columns={"last_change_by"})})
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
     * @ORM\Column(name="personal_id_number", type="string", length=45, nullable=true)
     */
    private $personalIdNumber;

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
    private $birthday = 'CURRENT_TIMESTAMP';

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
}
