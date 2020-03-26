<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrPayroll
 *
 * @ORM\Table(name="nmt_hr_payroll", indexes={@ORM\Index(name="nmt_hr_payroll_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_payroll_FK3_idx", columns={"input_id"}), @ORM\Index(name="nmt_hr_payroll_FK2_idx", columns={"period_id"})})
 * @ORM\Entity
 */
class NmtHrPayroll
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
     * @ORM\Column(name="period_name", type="string", length=45, nullable=true)
     */
    private $periodName;

    /**
     * @var string
     *
     * @ORM\Column(name="employee_name", type="string", length=45, nullable=true)
     */
    private $employeeName;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_gross", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumGross;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_net", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumNet;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_sso_employee", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumSsoEmployee;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_sso_employer", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumSsoEmployer;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_pit", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumPit;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_basic_salary", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumBasicSalary;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_productivity_bonus", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumProductivityBonus;

    /**
     * @var string
     *
     * @ORM\Column(name="sum_loyalty_bonus", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $sumLoyaltyBonus;

    /**
     * @var \Application\Entity\NmtHrEmployee
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEmployee")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * })
     */
    private $employee;

    /**
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="period_id", referencedColumnName="id")
     * })
     */
    private $period;

    /**
     * @var \Application\Entity\NmtHrPayrollInput
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrPayrollInput")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="input_id", referencedColumnName="id")
     * })
     */
    private $input;



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
     * Set periodName
     *
     * @param string $periodName
     *
     * @return NmtHrPayroll
     */
    public function setPeriodName($periodName)
    {
        $this->periodName = $periodName;

        return $this;
    }

    /**
     * Get periodName
     *
     * @return string
     */
    public function getPeriodName()
    {
        return $this->periodName;
    }

    /**
     * Set employeeName
     *
     * @param string $employeeName
     *
     * @return NmtHrPayroll
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtHrPayroll
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrPayroll
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtHrPayroll
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtHrPayroll
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
     * Set sumGross
     *
     * @param string $sumGross
     *
     * @return NmtHrPayroll
     */
    public function setSumGross($sumGross)
    {
        $this->sumGross = $sumGross;

        return $this;
    }

    /**
     * Get sumGross
     *
     * @return string
     */
    public function getSumGross()
    {
        return $this->sumGross;
    }

    /**
     * Set sumNet
     *
     * @param string $sumNet
     *
     * @return NmtHrPayroll
     */
    public function setSumNet($sumNet)
    {
        $this->sumNet = $sumNet;

        return $this;
    }

    /**
     * Get sumNet
     *
     * @return string
     */
    public function getSumNet()
    {
        return $this->sumNet;
    }

    /**
     * Set sumSsoEmployee
     *
     * @param string $sumSsoEmployee
     *
     * @return NmtHrPayroll
     */
    public function setSumSsoEmployee($sumSsoEmployee)
    {
        $this->sumSsoEmployee = $sumSsoEmployee;

        return $this;
    }

    /**
     * Get sumSsoEmployee
     *
     * @return string
     */
    public function getSumSsoEmployee()
    {
        return $this->sumSsoEmployee;
    }

    /**
     * Set sumSsoEmployer
     *
     * @param string $sumSsoEmployer
     *
     * @return NmtHrPayroll
     */
    public function setSumSsoEmployer($sumSsoEmployer)
    {
        $this->sumSsoEmployer = $sumSsoEmployer;

        return $this;
    }

    /**
     * Get sumSsoEmployer
     *
     * @return string
     */
    public function getSumSsoEmployer()
    {
        return $this->sumSsoEmployer;
    }

    /**
     * Set sumPit
     *
     * @param string $sumPit
     *
     * @return NmtHrPayroll
     */
    public function setSumPit($sumPit)
    {
        $this->sumPit = $sumPit;

        return $this;
    }

    /**
     * Get sumPit
     *
     * @return string
     */
    public function getSumPit()
    {
        return $this->sumPit;
    }

    /**
     * Set sumBasicSalary
     *
     * @param string $sumBasicSalary
     *
     * @return NmtHrPayroll
     */
    public function setSumBasicSalary($sumBasicSalary)
    {
        $this->sumBasicSalary = $sumBasicSalary;

        return $this;
    }

    /**
     * Get sumBasicSalary
     *
     * @return string
     */
    public function getSumBasicSalary()
    {
        return $this->sumBasicSalary;
    }

    /**
     * Set sumProductivityBonus
     *
     * @param string $sumProductivityBonus
     *
     * @return NmtHrPayroll
     */
    public function setSumProductivityBonus($sumProductivityBonus)
    {
        $this->sumProductivityBonus = $sumProductivityBonus;

        return $this;
    }

    /**
     * Get sumProductivityBonus
     *
     * @return string
     */
    public function getSumProductivityBonus()
    {
        return $this->sumProductivityBonus;
    }

    /**
     * Set sumLoyaltyBonus
     *
     * @param string $sumLoyaltyBonus
     *
     * @return NmtHrPayroll
     */
    public function setSumLoyaltyBonus($sumLoyaltyBonus)
    {
        $this->sumLoyaltyBonus = $sumLoyaltyBonus;

        return $this;
    }

    /**
     * Get sumLoyaltyBonus
     *
     * @return string
     */
    public function getSumLoyaltyBonus()
    {
        return $this->sumLoyaltyBonus;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrPayroll
     */
    public function setEmployee(\Application\Entity\NmtHrEmployee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \Application\Entity\NmtHrEmployee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set period
     *
     * @param \Application\Entity\NmtFinPostingPeriod $period
     *
     * @return NmtHrPayroll
     */
    public function setPeriod(\Application\Entity\NmtFinPostingPeriod $period = null)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return \Application\Entity\NmtFinPostingPeriod
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set input
     *
     * @param \Application\Entity\NmtHrPayrollInput $input
     *
     * @return NmtHrPayroll
     */
    public function setInput(\Application\Entity\NmtHrPayrollInput $input = null)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input
     *
     * @return \Application\Entity\NmtHrPayrollInput
     */
    public function getInput()
    {
        return $this->input;
    }
}
