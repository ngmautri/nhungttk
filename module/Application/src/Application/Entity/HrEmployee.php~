<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrEmployee
 *
 * @ORM\Table(name="hr_employee", indexes={@ORM\Index(name="hr_employee_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class HrEmployee
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
     * @ORM\Column(name="employee_code", type="string", length=45, nullable=false)
     */
    private $employeeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_name", type="string", length=100, nullable=false)
     */
    private $employeeName;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_name_local", type="string", length=45, nullable=false)
     */
    private $employeeNameLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_gender", type="string", length=45, nullable=false)
     */
    private $employeeGender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="employee_dob", type="datetime", nullable=false)
     */
    private $employeeDob = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="employee_last_status_id", type="integer", nullable=true)
     */
    private $employeeLastStatusId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set employeeCode
     *
     * @param string $employeeCode
     *
     * @return HrEmployee
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
     * @return HrEmployee
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
     * @return HrEmployee
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
     * Set employeeGender
     *
     * @param string $employeeGender
     *
     * @return HrEmployee
     */
    public function setEmployeeGender($employeeGender)
    {
        $this->employeeGender = $employeeGender;

        return $this;
    }

    /**
     * Get employeeGender
     *
     * @return string
     */
    public function getEmployeeGender()
    {
        return $this->employeeGender;
    }

    /**
     * Set employeeDob
     *
     * @param \DateTime $employeeDob
     *
     * @return HrEmployee
     */
    public function setEmployeeDob($employeeDob)
    {
        $this->employeeDob = $employeeDob;

        return $this;
    }

    /**
     * Get employeeDob
     *
     * @return \DateTime
     */
    public function getEmployeeDob()
    {
        return $this->employeeDob;
    }

    /**
     * Set employeeLastStatusId
     *
     * @param integer $employeeLastStatusId
     *
     * @return HrEmployee
     */
    public function setEmployeeLastStatusId($employeeLastStatusId)
    {
        $this->employeeLastStatusId = $employeeLastStatusId;

        return $this;
    }

    /**
     * Get employeeLastStatusId
     *
     * @return integer
     */
    public function getEmployeeLastStatusId()
    {
        return $this->employeeLastStatusId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return HrEmployee
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return HrEmployee
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return HrEmployee
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
}
