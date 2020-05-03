<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrPayrollInput
 *
 * @ORM\Table(name="nmt_hr_payroll_input", indexes={@ORM\Index(name="nmt_hr_payroll_input_FK1_idx", columns={"period_id"}), @ORM\Index(name="nmt_hr_payroll_input_FK2_idx", columns={"employee_id"}), @ORM\Index(name="nmt_hr_payroll_input_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_payroll_input_IDX1", columns={"revision_number"})})
 * @ORM\Entity
 */
class NmtHrPayrollInput
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
     * @var integer
     *
     * @ORM\Column(name="present_day", type="integer", nullable=true)
     */
    private $presentDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="out_of_office_day", type="integer", nullable=true)
     */
    private $outOfOfficeDay;

    /**
     * @var integer
     *
     * @ORM\Column(name="annual_leave", type="integer", nullable=true)
     */
    private $annualLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="approved_leave", type="integer", nullable=true)
     */
    private $approvedLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="unapproved_leave", type="integer", nullable=true)
     */
    private $unapprovedLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="sick_leave", type="integer", nullable=true)
     */
    private $sickLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="personal_paid_leave", type="integer", nullable=true)
     */
    private $personalPaidLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="maternity_leave", type="integer", nullable=true)
     */
    private $maternityLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="other_leave1", type="integer", nullable=true)
     */
    private $otherLeave1;

    /**
     * @var integer
     *
     * @ORM\Column(name="other_leave2", type="integer", nullable=true)
     */
    private $otherLeave2;

    /**
     * @var integer
     *
     * @ORM\Column(name="other_leave3", type="integer", nullable=true)
     */
    private $otherLeave3;

    /**
     * @var integer
     *
     * @ORM\Column(name="overtime1", type="integer", nullable=true)
     */
    private $overtime1;

    /**
     * @var integer
     *
     * @ORM\Column(name="overtime2", type="integer", nullable=true)
     */
    private $overtime2;

    /**
     * @var integer
     *
     * @ORM\Column(name="overtime3", type="integer", nullable=true)
     */
    private $overtime3;

    /**
     * @var integer
     *
     * @ORM\Column(name="overtime4", type="integer", nullable=true)
     */
    private $overtime4;

    /**
     * @var integer
     *
     * @ORM\Column(name="overtime5", type="integer", nullable=true)
     */
    private $overtime5;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_number", type="integer", nullable=true)
     */
    private $revisionNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

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
     * @var \DateTime
     *
     * @ORM\Column(name="consumed_on", type="datetime", nullable=true)
     */
    private $consumedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks_text", type="text", length=65535, nullable=true)
     */
    private $remarksText;

    /**
     * @var integer
     *
     * @ORM\Column(name="loaded_container", type="integer", nullable=true)
     */
    private $loadedContainer;

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
     * @return NmtHrPayrollInput
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
     * Set periodName
     *
     * @param string $periodName
     *
     * @return NmtHrPayrollInput
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
     * @return NmtHrPayrollInput
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
     * Set presentDay
     *
     * @param integer $presentDay
     *
     * @return NmtHrPayrollInput
     */
    public function setPresentDay($presentDay)
    {
        $this->presentDay = $presentDay;

        return $this;
    }

    /**
     * Get presentDay
     *
     * @return integer
     */
    public function getPresentDay()
    {
        return $this->presentDay;
    }

    /**
     * Set outOfOfficeDay
     *
     * @param integer $outOfOfficeDay
     *
     * @return NmtHrPayrollInput
     */
    public function setOutOfOfficeDay($outOfOfficeDay)
    {
        $this->outOfOfficeDay = $outOfOfficeDay;

        return $this;
    }

    /**
     * Get outOfOfficeDay
     *
     * @return integer
     */
    public function getOutOfOfficeDay()
    {
        return $this->outOfOfficeDay;
    }

    /**
     * Set annualLeave
     *
     * @param integer $annualLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setAnnualLeave($annualLeave)
    {
        $this->annualLeave = $annualLeave;

        return $this;
    }

    /**
     * Get annualLeave
     *
     * @return integer
     */
    public function getAnnualLeave()
    {
        return $this->annualLeave;
    }

    /**
     * Set approvedLeave
     *
     * @param integer $approvedLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setApprovedLeave($approvedLeave)
    {
        $this->approvedLeave = $approvedLeave;

        return $this;
    }

    /**
     * Get approvedLeave
     *
     * @return integer
     */
    public function getApprovedLeave()
    {
        return $this->approvedLeave;
    }

    /**
     * Set unapprovedLeave
     *
     * @param integer $unapprovedLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setUnapprovedLeave($unapprovedLeave)
    {
        $this->unapprovedLeave = $unapprovedLeave;

        return $this;
    }

    /**
     * Get unapprovedLeave
     *
     * @return integer
     */
    public function getUnapprovedLeave()
    {
        return $this->unapprovedLeave;
    }

    /**
     * Set sickLeave
     *
     * @param integer $sickLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setSickLeave($sickLeave)
    {
        $this->sickLeave = $sickLeave;

        return $this;
    }

    /**
     * Get sickLeave
     *
     * @return integer
     */
    public function getSickLeave()
    {
        return $this->sickLeave;
    }

    /**
     * Set personalPaidLeave
     *
     * @param integer $personalPaidLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setPersonalPaidLeave($personalPaidLeave)
    {
        $this->personalPaidLeave = $personalPaidLeave;

        return $this;
    }

    /**
     * Get personalPaidLeave
     *
     * @return integer
     */
    public function getPersonalPaidLeave()
    {
        return $this->personalPaidLeave;
    }

    /**
     * Set maternityLeave
     *
     * @param integer $maternityLeave
     *
     * @return NmtHrPayrollInput
     */
    public function setMaternityLeave($maternityLeave)
    {
        $this->maternityLeave = $maternityLeave;

        return $this;
    }

    /**
     * Get maternityLeave
     *
     * @return integer
     */
    public function getMaternityLeave()
    {
        return $this->maternityLeave;
    }

    /**
     * Set otherLeave1
     *
     * @param integer $otherLeave1
     *
     * @return NmtHrPayrollInput
     */
    public function setOtherLeave1($otherLeave1)
    {
        $this->otherLeave1 = $otherLeave1;

        return $this;
    }

    /**
     * Get otherLeave1
     *
     * @return integer
     */
    public function getOtherLeave1()
    {
        return $this->otherLeave1;
    }

    /**
     * Set otherLeave2
     *
     * @param integer $otherLeave2
     *
     * @return NmtHrPayrollInput
     */
    public function setOtherLeave2($otherLeave2)
    {
        $this->otherLeave2 = $otherLeave2;

        return $this;
    }

    /**
     * Get otherLeave2
     *
     * @return integer
     */
    public function getOtherLeave2()
    {
        return $this->otherLeave2;
    }

    /**
     * Set otherLeave3
     *
     * @param integer $otherLeave3
     *
     * @return NmtHrPayrollInput
     */
    public function setOtherLeave3($otherLeave3)
    {
        $this->otherLeave3 = $otherLeave3;

        return $this;
    }

    /**
     * Get otherLeave3
     *
     * @return integer
     */
    public function getOtherLeave3()
    {
        return $this->otherLeave3;
    }

    /**
     * Set overtime1
     *
     * @param integer $overtime1
     *
     * @return NmtHrPayrollInput
     */
    public function setOvertime1($overtime1)
    {
        $this->overtime1 = $overtime1;

        return $this;
    }

    /**
     * Get overtime1
     *
     * @return integer
     */
    public function getOvertime1()
    {
        return $this->overtime1;
    }

    /**
     * Set overtime2
     *
     * @param integer $overtime2
     *
     * @return NmtHrPayrollInput
     */
    public function setOvertime2($overtime2)
    {
        $this->overtime2 = $overtime2;

        return $this;
    }

    /**
     * Get overtime2
     *
     * @return integer
     */
    public function getOvertime2()
    {
        return $this->overtime2;
    }

    /**
     * Set overtime3
     *
     * @param integer $overtime3
     *
     * @return NmtHrPayrollInput
     */
    public function setOvertime3($overtime3)
    {
        $this->overtime3 = $overtime3;

        return $this;
    }

    /**
     * Get overtime3
     *
     * @return integer
     */
    public function getOvertime3()
    {
        return $this->overtime3;
    }

    /**
     * Set overtime4
     *
     * @param integer $overtime4
     *
     * @return NmtHrPayrollInput
     */
    public function setOvertime4($overtime4)
    {
        $this->overtime4 = $overtime4;

        return $this;
    }

    /**
     * Get overtime4
     *
     * @return integer
     */
    public function getOvertime4()
    {
        return $this->overtime4;
    }

    /**
     * Set overtime5
     *
     * @param integer $overtime5
     *
     * @return NmtHrPayrollInput
     */
    public function setOvertime5($overtime5)
    {
        $this->overtime5 = $overtime5;

        return $this;
    }

    /**
     * Get overtime5
     *
     * @return integer
     */
    public function getOvertime5()
    {
        return $this->overtime5;
    }

    /**
     * Set revisionNumber
     *
     * @param integer $revisionNumber
     *
     * @return NmtHrPayrollInput
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
     * Set status
     *
     * @param string $status
     *
     * @return NmtHrPayrollInput
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtHrPayrollInput
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
     * @return NmtHrPayrollInput
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
     * Set consumedOn
     *
     * @param \DateTime $consumedOn
     *
     * @return NmtHrPayrollInput
     */
    public function setConsumedOn($consumedOn)
    {
        $this->consumedOn = $consumedOn;

        return $this;
    }

    /**
     * Get consumedOn
     *
     * @return \DateTime
     */
    public function getConsumedOn()
    {
        return $this->consumedOn;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtHrPayrollInput
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtHrPayrollInput
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
     * Set remarksText
     *
     * @param string $remarksText
     *
     * @return NmtHrPayrollInput
     */
    public function setRemarksText($remarksText)
    {
        $this->remarksText = $remarksText;

        return $this;
    }

    /**
     * Get remarksText
     *
     * @return string
     */
    public function getRemarksText()
    {
        return $this->remarksText;
    }

    /**
     * Set loadedContainer
     *
     * @param integer $loadedContainer
     *
     * @return NmtHrPayrollInput
     */
    public function setLoadedContainer($loadedContainer)
    {
        $this->loadedContainer = $loadedContainer;

        return $this;
    }

    /**
     * Get loadedContainer
     *
     * @return integer
     */
    public function getLoadedContainer()
    {
        return $this->loadedContainer;
    }

    /**
     * Set period
     *
     * @param \Application\Entity\NmtFinPostingPeriod $period
     *
     * @return NmtHrPayrollInput
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
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrPayrollInput
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
     * @return NmtHrPayrollInput
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
