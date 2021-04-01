<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HrSalary
 *
 * @ORM\Table(name="hr_salary", indexes={@ORM\Index(name="nmt_hr_salary_FK4_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_salary_default_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="hr_salary_FK3_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class HrSalary
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

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
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;

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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * Set isDecorator
     *
     * @param boolean $isDecorator
     *
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * Set description
     *
     * @param string $description
     *
     * @return HrSalary
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return HrSalary
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return HrSalary
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
     * @return HrSalary
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
     * @return HrSalary
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
}
