<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrContractRevision
 *
 * @ORM\Table(name="nmt_hr_contract_revision", indexes={@ORM\Index(name="nmt_hr_contract_revision_FK1_idx", columns={"contract_id"}), @ORM\Index(name="nmt_hr_contract_revision_FK2_idx", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrContractRevision
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
     * @ORM\Column(name="revision_number", type="integer", nullable=false)
     */
    private $revisionNumber;

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
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="created_on", type="string", length=45, nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="postion", type="string", length=45, nullable=true)
     */
    private $postion;

    /**
     * @var integer
     *
     * @ORM\Column(name="postion_id", type="integer", nullable=true)
     */
    private $postionId;

    /**
     * @var string
     *
     * @ORM\Column(name="department_name", type="string", length=45, nullable=true)
     */
    private $departmentName;

    /**
     * @var integer
     *
     * @ORM\Column(name="departmnet_id", type="integer", nullable=true)
     */
    private $departmnetId;

    /**
     * @var integer
     *
     * @ORM\Column(name="working_time_from", type="integer", nullable=true)
     */
    private $workingTimeFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="wokring_time_to", type="string", length=45, nullable=true)
     */
    private $wokringTimeTo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="to_alert", type="boolean", nullable=true)
     */
    private $toAlert;

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
     * @return NmtHrContractRevision
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
     * Set revisionNumber
     *
     * @param integer $revisionNumber
     *
     * @return NmtHrContractRevision
     */
    public function setRevisionNumber($revisionNumber)
    {
        $this->revisionNumber = $revisionNumber;

        return $this;
    }

    /**
     * Get revisionNumber
     *
     * @return integer
     */
    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    /**
     * Set effectiveFrom
     *
     * @param \DateTime $effectiveFrom
     *
     * @return NmtHrContractRevision
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
     * @return NmtHrContractRevision
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtHrContractRevision
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
     * Set createdOn
     *
     * @param string $createdOn
     *
     * @return NmtHrContractRevision
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return string
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set postion
     *
     * @param string $postion
     *
     * @return NmtHrContractRevision
     */
    public function setPostion($postion)
    {
        $this->postion = $postion;

        return $this;
    }

    /**
     * Get postion
     *
     * @return string
     */
    public function getPostion()
    {
        return $this->postion;
    }

    /**
     * Set postionId
     *
     * @param integer $postionId
     *
     * @return NmtHrContractRevision
     */
    public function setPostionId($postionId)
    {
        $this->postionId = $postionId;

        return $this;
    }

    /**
     * Get postionId
     *
     * @return integer
     */
    public function getPostionId()
    {
        return $this->postionId;
    }

    /**
     * Set departmentName
     *
     * @param string $departmentName
     *
     * @return NmtHrContractRevision
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get departmentName
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set departmnetId
     *
     * @param integer $departmnetId
     *
     * @return NmtHrContractRevision
     */
    public function setDepartmnetId($departmnetId)
    {
        $this->departmnetId = $departmnetId;

        return $this;
    }

    /**
     * Get departmnetId
     *
     * @return integer
     */
    public function getDepartmnetId()
    {
        return $this->departmnetId;
    }

    /**
     * Set workingTimeFrom
     *
     * @param integer $workingTimeFrom
     *
     * @return NmtHrContractRevision
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
     * Set wokringTimeTo
     *
     * @param string $wokringTimeTo
     *
     * @return NmtHrContractRevision
     */
    public function setWokringTimeTo($wokringTimeTo)
    {
        $this->wokringTimeTo = $wokringTimeTo;

        return $this;
    }

    /**
     * Get wokringTimeTo
     *
     * @return string
     */
    public function getWokringTimeTo()
    {
        return $this->wokringTimeTo;
    }

    /**
     * Set toAlert
     *
     * @param boolean $toAlert
     *
     * @return NmtHrContractRevision
     */
    public function setToAlert($toAlert)
    {
        $this->toAlert = $toAlert;

        return $this;
    }

    /**
     * Get toAlert
     *
     * @return boolean
     */
    public function getToAlert()
    {
        return $this->toAlert;
    }

    /**
     * Set contract
     *
     * @param \Application\Entity\NmtHrContract $contract
     *
     * @return NmtHrContractRevision
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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrContractRevision
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
