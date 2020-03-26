<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrSalaryDecorator
 *
 * @ORM\Table(name="nmt_hr_salary_decorator", indexes={@ORM\Index(name="nmt_hr_salary_decorator_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_salary_decorator_FK2_idx", columns={"contract_revision_id"})})
 * @ORM\Entity
 */
class NmtHrSalaryDecorator
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
     * @ORM\Column(name="income_name", type="string", length=45, nullable=true)
     */
    private $incomeName;

    /**
     * @var string
     *
     * @ORM\Column(name="income_amount", type="decimal", precision=14, scale=6, nullable=false)
     */
    private $incomeAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pit_payable", type="boolean", nullable=true)
     */
    private $isPitPayable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sso_payable", type="boolean", nullable=true)
     */
    private $isSsoPayable;

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
     * @var string
     *
     * @ORM\Column(name="income_type", type="string", length=45, nullable=true)
     */
    private $incomeType;

    /**
     * @var string
     *
     * @ORM\Column(name="income_code", type="string", length=45, nullable=true)
     */
    private $incomeCode;

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
     * @var \Application\Entity\NmtHrContractRevision
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrContractRevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contract_revision_id", referencedColumnName="id")
     * })
     */
    private $contractRevision;



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
     * @return NmtHrSalaryDecorator
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
     * Set incomeName
     *
     * @param string $incomeName
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIncomeName($incomeName)
    {
        $this->incomeName = $incomeName;

        return $this;
    }

    /**
     * Get incomeName
     *
     * @return string
     */
    public function getIncomeName()
    {
        return $this->incomeName;
    }

    /**
     * Set incomeAmount
     *
     * @param string $incomeAmount
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIncomeAmount($incomeAmount)
    {
        $this->incomeAmount = $incomeAmount;

        return $this;
    }

    /**
     * Get incomeAmount
     *
     * @return string
     */
    public function getIncomeAmount()
    {
        return $this->incomeAmount;
    }

    /**
     * Set isPitPayable
     *
     * @param boolean $isPitPayable
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIsPitPayable($isPitPayable)
    {
        $this->isPitPayable = $isPitPayable;

        return $this;
    }

    /**
     * Get isPitPayable
     *
     * @return boolean
     */
    public function getIsPitPayable()
    {
        return $this->isPitPayable;
    }

    /**
     * Set isSsoPayable
     *
     * @param boolean $isSsoPayable
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIsSsoPayable($isSsoPayable)
    {
        $this->isSsoPayable = $isSsoPayable;

        return $this;
    }

    /**
     * Get isSsoPayable
     *
     * @return boolean
     */
    public function getIsSsoPayable()
    {
        return $this->isSsoPayable;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrSalaryDecorator
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
     * @return NmtHrSalaryDecorator
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
     * Set incomeType
     *
     * @param string $incomeType
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIncomeType($incomeType)
    {
        $this->incomeType = $incomeType;

        return $this;
    }

    /**
     * Get incomeType
     *
     * @return string
     */
    public function getIncomeType()
    {
        return $this->incomeType;
    }

    /**
     * Set incomeCode
     *
     * @param string $incomeCode
     *
     * @return NmtHrSalaryDecorator
     */
    public function setIncomeCode($incomeCode)
    {
        $this->incomeCode = $incomeCode;

        return $this;
    }

    /**
     * Get incomeCode
     *
     * @return string
     */
    public function getIncomeCode()
    {
        return $this->incomeCode;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrSalaryDecorator
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
     * Set contractRevision
     *
     * @param \Application\Entity\NmtHrContractRevision $contractRevision
     *
     * @return NmtHrSalaryDecorator
     */
    public function setContractRevision(\Application\Entity\NmtHrContractRevision $contractRevision = null)
    {
        $this->contractRevision = $contractRevision;

        return $this;
    }

    /**
     * Get contractRevision
     *
     * @return \Application\Entity\NmtHrContractRevision
     */
    public function getContractRevision()
    {
        return $this->contractRevision;
    }
}
