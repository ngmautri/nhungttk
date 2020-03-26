<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrEmployeeSalary
 *
 * @ORM\Table(name="nmt_hr_employee_salary", indexes={@ORM\Index(name="nmt_hr_employee_salary_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_employee_salary_FK2_idx", columns={"evaluation_id"}), @ORM\Index(name="nmt_hr_employee_salary_FK3_idx", columns={"annual_review"})})
 * @ORM\Entity
 */
class NmtHrEmployeeSalary
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
     * @ORM\Column(name="basic_salary", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $basicSalary;

    /**
     * @var string
     *
     * @ORM\Column(name="transporation_allowance", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $transporationAllowance;

    /**
     * @var string
     *
     * @ORM\Column(name="housing_allowance", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $housingAllowance;

    /**
     * @var string
     *
     * @ORM\Column(name="nmt_hr_salarycol", type="string", length=45, nullable=true)
     */
    private $nmtHrSalarycol;

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
     * @var \Application\Entity\NmtHrEvaluation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrEvaluation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="evaluation_id", referencedColumnName="id")
     * })
     */
    private $evaluation;

    /**
     * @var \Application\Entity\NmtHrAnnualReview
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrAnnualReview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="annual_review", referencedColumnName="id")
     * })
     */
    private $annualReview;



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
     * Set basicSalary
     *
     * @param string $basicSalary
     *
     * @return NmtHrEmployeeSalary
     */
    public function setBasicSalary($basicSalary)
    {
        $this->basicSalary = $basicSalary;

        return $this;
    }

    /**
     * Get basicSalary
     *
     * @return string
     */
    public function getBasicSalary()
    {
        return $this->basicSalary;
    }

    /**
     * Set transporationAllowance
     *
     * @param string $transporationAllowance
     *
     * @return NmtHrEmployeeSalary
     */
    public function setTransporationAllowance($transporationAllowance)
    {
        $this->transporationAllowance = $transporationAllowance;

        return $this;
    }

    /**
     * Get transporationAllowance
     *
     * @return string
     */
    public function getTransporationAllowance()
    {
        return $this->transporationAllowance;
    }

    /**
     * Set housingAllowance
     *
     * @param string $housingAllowance
     *
     * @return NmtHrEmployeeSalary
     */
    public function setHousingAllowance($housingAllowance)
    {
        $this->housingAllowance = $housingAllowance;

        return $this;
    }

    /**
     * Get housingAllowance
     *
     * @return string
     */
    public function getHousingAllowance()
    {
        return $this->housingAllowance;
    }

    /**
     * Set nmtHrSalarycol
     *
     * @param string $nmtHrSalarycol
     *
     * @return NmtHrEmployeeSalary
     */
    public function setNmtHrSalarycol($nmtHrSalarycol)
    {
        $this->nmtHrSalarycol = $nmtHrSalarycol;

        return $this;
    }

    /**
     * Get nmtHrSalarycol
     *
     * @return string
     */
    public function getNmtHrSalarycol()
    {
        return $this->nmtHrSalarycol;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrEmployeeSalary
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
     * Set evaluation
     *
     * @param \Application\Entity\NmtHrEvaluation $evaluation
     *
     * @return NmtHrEmployeeSalary
     */
    public function setEvaluation(\Application\Entity\NmtHrEvaluation $evaluation = null)
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    /**
     * Get evaluation
     *
     * @return \Application\Entity\NmtHrEvaluation
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * Set annualReview
     *
     * @param \Application\Entity\NmtHrAnnualReview $annualReview
     *
     * @return NmtHrEmployeeSalary
     */
    public function setAnnualReview(\Application\Entity\NmtHrAnnualReview $annualReview = null)
    {
        $this->annualReview = $annualReview;

        return $this;
    }

    /**
     * Get annualReview
     *
     * @return \Application\Entity\NmtHrAnnualReview
     */
    public function getAnnualReview()
    {
        return $this->annualReview;
    }
}
