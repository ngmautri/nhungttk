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
