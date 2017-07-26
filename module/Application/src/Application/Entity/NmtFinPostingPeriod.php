<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtFinPostingPeriod
 *
 * @ORM\Table(name="nmt_fin_posting_period", uniqueConstraints={@ORM\UniqueConstraint(name="posting_from_date_UNIQUE", columns={"posting_from_date"}), @ORM\UniqueConstraint(name="posting_to_date_UNIQUE", columns={"posting_to_date"})}, indexes={@ORM\Index(name="nmt_fin_posting_period_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_fin_posting_period_FK2_idx", columns={"last_change_by"})})
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
}
