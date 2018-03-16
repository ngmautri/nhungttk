<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrContract
 *
 * @ORM\Table(name="nmt_hr_contract", indexes={@ORM\Index(name="nmt_hr_contract_FK1_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_contract_IDX1", columns={"token"})})
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
     * @var string
     *
     * @ORM\Column(name="contract_number", type="string", length=45, nullable=true)
     */
    private $contractNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_date", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="termination_date", type="datetime", nullable=true)
     */
    private $terminationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="is_active", type="string", length=45, nullable=true)
     */
    private $isActive;

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
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_revision_id", type="integer", nullable=true)
     */
    private $lastRevisionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="termination_id", type="integer", nullable=true)
     */
    private $terminationId;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

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
     * @param string $contractDate
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
     * @return string
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
     * Set isActive
     *
     * @param string $isActive
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
     * @return string
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtHrContract
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
}
