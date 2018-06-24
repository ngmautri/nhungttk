<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtFinPostingPeriod
 *
 * @ORM\Table(name="nmt_fin_posting_period", uniqueConstraints={@ORM\UniqueConstraint(name="posting_from_date_UNIQUE", columns={"posting_from_date"}), @ORM\UniqueConstraint(name="posting_to_date_UNIQUE", columns={"posting_to_date"})}, indexes={@ORM\Index(name="nmt_fin_posting_period_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_fin_posting_period_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_fin_posting_period_IDX1", columns={"posting_from_date"}), @ORM\Index(name="nmt_fin_posting_period_IDX2", columns={"posting_to_date"}), @ORM\Index(name="nmt_fin_posting_period_FK3_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtFinPostingPeriod
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
     * @ORM\Column(name="period_code", type="string", length=20, nullable=false)
     */
    private $periodCode;

    /**
     * @var string
     *
     * @ORM\Column(name="period_name", type="string", length=20, nullable=false)
     */
    private $periodName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_from_date", type="datetime", nullable=true)
     */
    private $postingFromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_to_date", type="datetime", nullable=true)
     */
    private $postingToDate;

    /**
     * @var string
     *
     * @ORM\Column(name="period_status", type="string", nullable=false)
     */
    private $periodStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="plan_working_days", type="integer", nullable=true)
     */
    private $planWorkingDays;

    /**
     * @var integer
     *
     * @ORM\Column(name="actual_workding_days", type="integer", nullable=true)
     */
    private $actualWorkdingDays;

    /**
     * @var integer
     *
     * @ORM\Column(name="cooperate_leave", type="integer", nullable=true)
     */
    private $cooperateLeave;

    /**
     * @var integer
     *
     * @ORM\Column(name="national_holidays", type="integer", nullable=true)
     */
    private $nationalHolidays;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=200, nullable=true)
     */
    private $remarks;

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
     * @return NmtFinPostingPeriod
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
     * Set periodCode
     *
     * @param string $periodCode
     *
     * @return NmtFinPostingPeriod
     */
    public function setPeriodCode($periodCode)
    {
        $this->periodCode = $periodCode;

        return $this;
    }

    /**
     * Get periodCode
     *
     * @return string
     */
    public function getPeriodCode()
    {
        return $this->periodCode;
    }

    /**
     * Set periodName
     *
     * @param string $periodName
     *
     * @return NmtFinPostingPeriod
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
     * Set postingFromDate
     *
     * @param \DateTime $postingFromDate
     *
     * @return NmtFinPostingPeriod
     */
    public function setPostingFromDate($postingFromDate)
    {
        $this->postingFromDate = $postingFromDate;

        return $this;
    }

    /**
     * Get postingFromDate
     *
     * @return \DateTime
     */
    public function getPostingFromDate()
    {
        return $this->postingFromDate;
    }

    /**
     * Set postingToDate
     *
     * @param \DateTime $postingToDate
     *
     * @return NmtFinPostingPeriod
     */
    public function setPostingToDate($postingToDate)
    {
        $this->postingToDate = $postingToDate;

        return $this;
    }

    /**
     * Get postingToDate
     *
     * @return \DateTime
     */
    public function getPostingToDate()
    {
        return $this->postingToDate;
    }

    /**
     * Set periodStatus
     *
     * @param string $periodStatus
     *
     * @return NmtFinPostingPeriod
     */
    public function setPeriodStatus($periodStatus)
    {
        $this->periodStatus = $periodStatus;

        return $this;
    }

    /**
     * Get periodStatus
     *
     * @return string
     */
    public function getPeriodStatus()
    {
        return $this->periodStatus;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtFinPostingPeriod
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtFinPostingPeriod
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
     * Set planWorkingDays
     *
     * @param integer $planWorkingDays
     *
     * @return NmtFinPostingPeriod
     */
    public function setPlanWorkingDays($planWorkingDays)
    {
        $this->planWorkingDays = $planWorkingDays;

        return $this;
    }

    /**
     * Get planWorkingDays
     *
     * @return integer
     */
    public function getPlanWorkingDays()
    {
        return $this->planWorkingDays;
    }

    /**
     * Set actualWorkdingDays
     *
     * @param integer $actualWorkdingDays
     *
     * @return NmtFinPostingPeriod
     */
    public function setActualWorkdingDays($actualWorkdingDays)
    {
        $this->actualWorkdingDays = $actualWorkdingDays;

        return $this;
    }

    /**
     * Get actualWorkdingDays
     *
     * @return integer
     */
    public function getActualWorkdingDays()
    {
        return $this->actualWorkdingDays;
    }

    /**
     * Set cooperateLeave
     *
     * @param integer $cooperateLeave
     *
     * @return NmtFinPostingPeriod
     */
    public function setCooperateLeave($cooperateLeave)
    {
        $this->cooperateLeave = $cooperateLeave;

        return $this;
    }

    /**
     * Get cooperateLeave
     *
     * @return integer
     */
    public function getCooperateLeave()
    {
        return $this->cooperateLeave;
    }

    /**
     * Set nationalHolidays
     *
     * @param integer $nationalHolidays
     *
     * @return NmtFinPostingPeriod
     */
    public function setNationalHolidays($nationalHolidays)
    {
        $this->nationalHolidays = $nationalHolidays;

        return $this;
    }

    /**
     * Get nationalHolidays
     *
     * @return integer
     */
    public function getNationalHolidays()
    {
        return $this->nationalHolidays;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtFinPostingPeriod
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtFinPostingPeriod
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
     * @return NmtFinPostingPeriod
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
     * @return NmtFinPostingPeriod
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
