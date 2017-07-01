<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrFingerscan
 *
 * @ORM\Table(name="nmt_hr_fingerscan", uniqueConstraints={@ORM\UniqueConstraint(name="employee_id_UNIQUE", columns={"employee_id"})})
 * @ORM\Entity
 */
class NmtHrFingerscan
{
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="attendance_date", type="datetime", nullable=true)
     */
    private $attendanceDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=true)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clock_in", type="time", nullable=true)
     */
    private $clockIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clock_out", type="time", nullable=true)
     */
    private $clockOut;

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
     * @var integer
     *
     * @ORM\Column(name="attendance_type", type="integer", nullable=true)
     */
    private $attendanceType;

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
     * @var \Application\Entity\NmtHrAttendanceType
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Application\Entity\NmtHrAttendanceType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $id;



    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtHrFingerscan
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
     * Set attendanceDate
     *
     * @param \DateTime $attendanceDate
     *
     * @return NmtHrFingerscan
     */
    public function setAttendanceDate($attendanceDate)
    {
        $this->attendanceDate = $attendanceDate;

        return $this;
    }

    /**
     * Get attendanceDate
     *
     * @return \DateTime
     */
    public function getAttendanceDate()
    {
        return $this->attendanceDate;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return NmtHrFingerscan
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return NmtHrFingerscan
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set clockIn
     *
     * @param \DateTime $clockIn
     *
     * @return NmtHrFingerscan
     */
    public function setClockIn($clockIn)
    {
        $this->clockIn = $clockIn;

        return $this;
    }

    /**
     * Get clockIn
     *
     * @return \DateTime
     */
    public function getClockIn()
    {
        return $this->clockIn;
    }

    /**
     * Set clockOut
     *
     * @param \DateTime $clockOut
     *
     * @return NmtHrFingerscan
     */
    public function setClockOut($clockOut)
    {
        $this->clockOut = $clockOut;

        return $this;
    }

    /**
     * Get clockOut
     *
     * @return \DateTime
     */
    public function getClockOut()
    {
        return $this->clockOut;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtHrFingerscan
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
     * @return NmtHrFingerscan
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
     * Set attendanceType
     *
     * @param integer $attendanceType
     *
     * @return NmtHrFingerscan
     */
    public function setAttendanceType($attendanceType)
    {
        $this->attendanceType = $attendanceType;

        return $this;
    }

    /**
     * Get attendanceType
     *
     * @return integer
     */
    public function getAttendanceType()
    {
        return $this->attendanceType;
    }

    /**
     * Set employee
     *
     * @param \Application\Entity\NmtHrEmployee $employee
     *
     * @return NmtHrFingerscan
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
     * Set id
     *
     * @param \Application\Entity\NmtHrAttendanceType $id
     *
     * @return NmtHrFingerscan
     */
    public function setId(\Application\Entity\NmtHrAttendanceType $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return \Application\Entity\NmtHrAttendanceType
     */
    public function getId()
    {
        return $this->id;
    }
}
