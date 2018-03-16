<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrSalary
 *
 * @ORM\Table(name="nmt_hr_salary", indexes={@ORM\Index(name="nmt_hr_salary_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_salary_FK2_idx", columns={"contract_revision_id"})})
 * @ORM\Entity
 */
class NmtHrSalary
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
     * @ORM\Column(name="salary_type", type="string", nullable=true)
     */
    private $salaryType;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_code", type="string", length=45, nullable=true)
     */
    private $salaryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_name", type="string", length=45, nullable=true)
     */
    private $salaryName;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_amount", type="decimal", precision=14, scale=6, nullable=false)
     */
    private $salaryAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_decorator", type="boolean", nullable=true)
     */
    private $isDecorator;

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
     * @var boolean
     *
     * @ORM\Column(name="is_payable", type="boolean", nullable=true)
     */
    private $isPayable;

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
     * @ORM\Column(name="curent_state", type="string", length=45, nullable=true)
     */
    private $curentState;

    /**
     * @var string
     *
     * @ORM\Column(name="decorator_factory", type="string", length=255, nullable=true)
     */
    private $decoratorFactory;

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
     * @return NmtHrSalary
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
     * Set salaryType
     *
     * @param string $salaryType
     *
     * @return NmtHrSalary
     */
    public function setSalaryType($salaryType)
    {
        $this->salaryType = $salaryType;

        return $this;
    }

    /**
     * Get salaryType
     *
     * @return string
     */
    public function getSalaryType()
    {
        return $this->salaryType;
    }

    /**
     * Set salaryCode
     *
     * @param string $salaryCode
     *
     * @return NmtHrSalary
     */
    public function setSalaryCode($salaryCode)
    {
        $this->salaryCode = $salaryCode;

        return $this;
    }

    /**
     * Get salaryCode
     *
     * @return string
     */
    public function getSalaryCode()
    {
        return $this->salaryCode;
    }

    /**
     * Set salaryName
     *
     * @param string $salaryName
     *
     * @return NmtHrSalary
     */
    public function setSalaryName($salaryName)
    {
        $this->salaryName = $salaryName;

        return $this;
    }

    /**
     * Get salaryName
     *
     * @return string
     */
    public function getSalaryName()
    {
        return $this->salaryName;
    }

    /**
     * Set salaryAmount
     *
     * @param string $salaryAmount
     *
     * @return NmtHrSalary
     */
    public function setSalaryAmount($salaryAmount)
    {
        $this->salaryAmount = $salaryAmount;

        return $this;
    }

    /**
     * Get salaryAmount
     *
     * @return string
     */
    public function getSalaryAmount()
    {
        return $this->salaryAmount;
    }

    /**
     * Set isDecorator
     *
     * @param boolean $isDecorator
     *
     * @return NmtHrSalary
     */
    public function setIsDecorator($isDecorator)
    {
        $this->isDecorator = $isDecorator;

        return $this;
    }

    /**
     * Get isDecorator
     *
     * @return boolean
     */
    public function getIsDecorator()
    {
        return $this->isDecorator;
    }

    /**
     * Set isPitPayable
     *
     * @param boolean $isPitPayable
     *
     * @return NmtHrSalary
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
     * @return NmtHrSalary
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
     * Set isPayable
     *
     * @param boolean $isPayable
     *
     * @return NmtHrSalary
     */
    public function setIsPayable($isPayable)
    {
        $this->isPayable = $isPayable;

        return $this;
    }

    /**
     * Get isPayable
     *
     * @return boolean
     */
    public function getIsPayable()
    {
        return $this->isPayable;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrSalary
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
     * @return NmtHrSalary
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
     * Set curentState
     *
     * @param string $curentState
     *
     * @return NmtHrSalary
     */
    public function setCurentState($curentState)
    {
        $this->curentState = $curentState;

        return $this;
    }

    /**
     * Get curentState
     *
     * @return string
     */
    public function getCurentState()
    {
        return $this->curentState;
    }

    /**
     * Set decoratorFactory
     *
     * @param string $decoratorFactory
     *
     * @return NmtHrSalary
     */
    public function setDecoratorFactory($decoratorFactory)
    {
        $this->decoratorFactory = $decoratorFactory;

        return $this;
    }

    /**
     * Get decoratorFactory
     *
     * @return string
     */
    public function getDecoratorFactory()
    {
        return $this->decoratorFactory;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrSalary
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
     * @return NmtHrSalary
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
