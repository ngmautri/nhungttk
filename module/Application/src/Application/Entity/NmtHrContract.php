<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrContract
 *
 * @ORM\Table(name="nmt_hr_contract", indexes={@ORM\Index(name="nmt_hr_contract_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_contract_IDX1", columns={"token"}), @ORM\Index(name="nmt_hr_contract_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_contract_FK3_idx", columns={"currency_id"}), @ORM\Index(name="nmt_hr_contract_FK4_idx", columns={"position_id"}), @ORM\Index(name="nmt_hr_contract_FK5_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtHrContract
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
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_revision_id", type="integer", nullable=true)
     */
    private $lastRevisionId;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_number", type="string", length=45, nullable=true)
     */
    private $contractNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contract_date", type="datetime", nullable=true)
     */
    private $contractDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="siging_date", type="datetime", nullable=true)
     */
    private $sigingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_working_date", type="datetime", nullable=true)
     */
    private $startWorkingDate;

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
     * @var integer
     *
     * @ORM\Column(name="working_time_from", type="integer", nullable=true)
     */
    private $workingTimeFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="working_time_to", type="integer", nullable=true)
     */
    private $workingTimeTo;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="position_name", type="string", length=100, nullable=true)
     */
    private $positionName;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="basic_salary", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $basicSalary;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * @var integer
     *
     * @ORM\Column(name="termination_id", type="integer", nullable=true)
     */
    private $terminationId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termination_notice_date", type="datetime", nullable=true)
     */
    private $terminationNoticeDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termination_date", type="datetime", nullable=true)
     */
    private $terminationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="termination_type", type="string", nullable=true)
     */
    private $terminationType;

    /**
     * @var string
     *
     * @ORM\Column(name="termination_reason", type="text", length=65535, nullable=true)
     */
    private $terminationReason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_working_date", type="datetime", nullable=true)
     */
    private $lastWorkingDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_terminated", type="boolean", nullable=true)
     */
    private $isTerminated;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_status", type="string", length=45, nullable=true)
     */
    private $contractStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_gross", type="boolean", nullable=true)
     */
    private $isGross;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    private $currency;

    /**
     * @var \Application\Entity\NmtHrPosition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtHrPosition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="position_id", referencedColumnName="id")
     * })
     */
    private $position;

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
     * Set token
     *
     * @param string $token
     *
     * @return NmtHrContract
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtHrContract
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
     * Set lastRevisionId
     *
     * @param integer $lastRevisionId
     *
     * @return NmtHrContract
     */
    public function setLastRevisionId($lastRevisionId)
    {
        $this->lastRevisionId = $lastRevisionId;

        return $this;
    }

    /**
     * Get lastRevisionId
     *
     * @return integer
     */
    public function getLastRevisionId()
    {
        return $this->lastRevisionId;
    }

    /**
     * Set contractNumber
     *
     * @param string $contractNumber
     *
     * @return NmtHrContract
     */
    public function setContractNumber($contractNumber)
    {
        $this->contractNumber = $contractNumber;

        return $this;
    }

    /**
     * Get contractNumber
     *
     * @return string
     */
    public function getContractNumber()
    {
        return $this->contractNumber;
    }

    /**
     * Set contractDate
     *
     * @param \DateTime $contractDate
     *
     * @return NmtHrContract
     */
    public function setContractDate($contractDate)
    {
        $this->contractDate = $contractDate;

        return $this;
    }

    /**
     * Get contractDate
     *
     * @return \DateTime
     */
    public function getContractDate()
    {
        return $this->contractDate;
    }

    /**
     * Set sigingDate
     *
     * @param \DateTime $sigingDate
     *
     * @return NmtHrContract
     */
    public function setSigingDate($sigingDate)
    {
        $this->sigingDate = $sigingDate;

        return $this;
    }

    /**
     * Get sigingDate
     *
     * @return \DateTime
     */
    public function getSigingDate()
    {
        return $this->sigingDate;
    }

    /**
     * Set startWorkingDate
     *
     * @param \DateTime $startWorkingDate
     *
     * @return NmtHrContract
     */
    public function setStartWorkingDate($startWorkingDate)
    {
        $this->startWorkingDate = $startWorkingDate;

        return $this;
    }

    /**
     * Get startWorkingDate
     *
     * @return \DateTime
     */
    public function getStartWorkingDate()
    {
        return $this->startWorkingDate;
    }

    /**
     * Set effectiveFrom
     *
     * @param \DateTime $effectiveFrom
     *
     * @return NmtHrContract
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
     * @return NmtHrContract
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
     * Set workingTimeFrom
     *
     * @param integer $workingTimeFrom
     *
     * @return NmtHrContract
     */
    public function setWorkingTimeFrom($workingTimeFrom)
    {
        $this->workingTimeFrom = $workingTimeFrom;

        return $this;
    }

    /**
     * Get workingTimeFrom
     *
     * @return integer
     */
    public function getWorkingTimeFrom()
    {
        return $this->workingTimeFrom;
    }

    /**
     * Set workingTimeTo
     *
     * @param integer $workingTimeTo
     *
     * @return NmtHrContract
     */
    public function setWorkingTimeTo($workingTimeTo)
    {
        $this->workingTimeTo = $workingTimeTo;

        return $this;
    }

    /**
     * Get workingTimeTo
     *
     * @return integer
     */
    public function getWorkingTimeTo()
    {
        return $this->workingTimeTo;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NmtHrContract
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set positionName
     *
     * @param string $positionName
     *
     * @return NmtHrContract
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;

        return $this;
    }

    /**
     * Get positionName
     *
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtHrContract
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
     * Set basicSalary
     *
     * @param string $basicSalary
     *
     * @return NmtHrContract
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtHrContract
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrContract
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
     * @return NmtHrContract
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
     * Set terminationId
     *
     * @param integer $terminationId
     *
     * @return NmtHrContract
     */
    public function setTerminationId($terminationId)
    {
        $this->terminationId = $terminationId;

        return $this;
    }

    /**
     * Get terminationId
     *
     * @return integer
     */
    public function getTerminationId()
    {
        return $this->terminationId;
    }

    /**
     * Set terminationNoticeDate
     *
     * @param \DateTime $terminationNoticeDate
     *
     * @return NmtHrContract
     */
    public function setTerminationNoticeDate($terminationNoticeDate)
    {
        $this->terminationNoticeDate = $terminationNoticeDate;

        return $this;
    }

    /**
     * Get terminationNoticeDate
     *
     * @return \DateTime
     */
    public function getTerminationNoticeDate()
    {
        return $this->terminationNoticeDate;
    }

    /**
     * Set terminationDate
     *
     * @param \DateTime $terminationDate
     *
     * @return NmtHrContract
     */
    public function setTerminationDate($terminationDate)
    {
        $this->terminationDate = $terminationDate;

        return $this;
    }

    /**
     * Get terminationDate
     *
     * @return \DateTime
     */
    public function getTerminationDate()
    {
        return $this->terminationDate;
    }

    /**
     * Set terminationType
     *
     * @param string $terminationType
     *
     * @return NmtHrContract
     */
    public function setTerminationType($terminationType)
    {
        $this->terminationType = $terminationType;

        return $this;
    }

    /**
     * Get terminationType
     *
     * @return string
     */
    public function getTerminationType()
    {
        return $this->terminationType;
    }

    /**
     * Set terminationReason
     *
     * @param string $terminationReason
     *
     * @return NmtHrContract
     */
    public function setTerminationReason($terminationReason)
    {
        $this->terminationReason = $terminationReason;

        return $this;
    }

    /**
     * Get terminationReason
     *
     * @return string
     */
    public function getTerminationReason()
    {
        return $this->terminationReason;
    }

    /**
     * Set lastWorkingDate
     *
     * @param \DateTime $lastWorkingDate
     *
     * @return NmtHrContract
     */
    public function setLastWorkingDate($lastWorkingDate)
    {
        $this->lastWorkingDate = $lastWorkingDate;

        return $this;
    }

    /**
     * Get lastWorkingDate
     *
     * @return \DateTime
     */
    public function getLastWorkingDate()
    {
        return $this->lastWorkingDate;
    }

    /**
     * Set isTerminated
     *
     * @param boolean $isTerminated
     *
     * @return NmtHrContract
     */
    public function setIsTerminated($isTerminated)
    {
        $this->isTerminated = $isTerminated;

        return $this;
    }

    /**
     * Get isTerminated
     *
     * @return boolean
     */
    public function getIsTerminated()
    {
        return $this->isTerminated;
    }

    /**
     * Set contractStatus
     *
     * @param string $contractStatus
     *
     * @return NmtHrContract
     */
    public function setContractStatus($contractStatus)
    {
        $this->contractStatus = $contractStatus;

        return $this;
    }

    /**
     * Get contractStatus
     *
     * @return string
     */
    public function getContractStatus()
    {
        return $this->contractStatus;
    }

    /**
     * Set isGross
     *
     * @param boolean $isGross
     *
     * @return NmtHrContract
     */
    public function setIsGross($isGross)
    {
        $this->isGross = $isGross;

        return $this;
    }

    /**
     * Get isGross
     *
     * @return boolean
     */
    public function getIsGross()
    {
        return $this->isGross;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtHrContract
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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrContract
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtHrContract
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
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return NmtHrContract
     */
    public function setCurrency(\Application\Entity\NmtApplicationCurrency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set position
     *
     * @param \Application\Entity\NmtHrPosition $position
     *
     * @return NmtHrContract
     */
    public function setPosition(\Application\Entity\NmtHrPosition $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \Application\Entity\NmtHrPosition
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtHrContract
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
