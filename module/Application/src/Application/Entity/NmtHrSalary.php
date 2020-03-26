<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrSalary
 *
 * @ORM\Table(name="nmt_hr_salary", indexes={@ORM\Index(name="nmt_hr_salary_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_salary_FK3_idx", columns={"contract_id"}), @ORM\Index(name="nmt_hr_salary_FK4_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_hr_salary_FK5_idx", columns={"default_salary_id"}), @ORM\Index(name="nmt_hr_salary_FK6_idx", columns={"created_by"})})
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
     * @var \DateTime
     *
     * @ORM\Column(name="effective_from", type="datetime", nullable=true)
     */
    private $effectiveFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_to", type="datetime", nullable=true)
     */
    private $effectiveTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_frequency", type="string", length=45, nullable=true)
     */
    private $paymentFrequency;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_factory", type="string", length=255, nullable=true)
     */
    private $salaryFactory;

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
     * @var \Application\Entity\NmtHrContract
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrContract")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contract_id", referencedColumnName="id")
     * })
     */
    private $contract;

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
     * @var \Application\Entity\NmtHrSalaryDefault
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrSalaryDefault")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_salary_id", referencedColumnName="id")
     * })
     */
    private $defaultSalary;

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
     * Set effectiveFrom
     *
     * @param \DateTime $effectiveFrom
     *
     * @return NmtHrSalary
     */
    public function setEffectiveFrom($effectiveFrom)
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }

    /**
     * Get effectiveFrom
     *
     * @return \DateTime
     */
    public function getEffectiveFrom()
    {
        return $this->effectiveFrom;
    }

    /**
     * Set effectiveTo
     *
     * @param \DateTime $effectiveTo
     *
     * @return NmtHrSalary
     */
    public function setEffectiveTo($effectiveTo)
    {
        $this->effectiveTo = $effectiveTo;

        return $this;
    }

    /**
     * Get effectiveTo
     *
     * @return \DateTime
     */
    public function getEffectiveTo()
    {
        return $this->effectiveTo;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtHrSalary
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtHrSalary
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtHrSalary
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set paymentFrequency
     *
     * @param string $paymentFrequency
     *
     * @return NmtHrSalary
     */
    public function setPaymentFrequency($paymentFrequency)
    {
        $this->paymentFrequency = $paymentFrequency;

        return $this;
    }

    /**
     * Get paymentFrequency
     *
     * @return string
     */
    public function getPaymentFrequency()
    {
        return $this->paymentFrequency;
    }

    /**
     * Set salaryFactory
     *
     * @param string $salaryFactory
     *
     * @return NmtHrSalary
     */
    public function setSalaryFactory($salaryFactory)
    {
        $this->salaryFactory = $salaryFactory;

        return $this;
    }

    /**
     * Get salaryFactory
     *
     * @return string
     */
    public function getSalaryFactory()
    {
        return $this->salaryFactory;
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
     * Set contract
     *
     * @param \Application\Entity\NmtHrContract $contract
     *
     * @return NmtHrSalary
     */
    public function setContract(\Application\Entity\NmtHrContract $contract = null)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Get contract
     *
     * @return \Application\Entity\NmtHrContract
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtHrSalary
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
     * Set defaultSalary
     *
     * @param \Application\Entity\NmtHrSalaryDefault $defaultSalary
     *
     * @return NmtHrSalary
     */
    public function setDefaultSalary(\Application\Entity\NmtHrSalaryDefault $defaultSalary = null)
    {
        $this->defaultSalary = $defaultSalary;

        return $this;
    }

    /**
     * Get defaultSalary
     *
     * @return \Application\Entity\NmtHrSalaryDefault
     */
    public function getDefaultSalary()
    {
        return $this->defaultSalary;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtHrSalary
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
