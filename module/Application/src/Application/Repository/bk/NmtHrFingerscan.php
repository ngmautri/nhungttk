<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtHrFingerscan
 *
 * @ORM\Table(name="nmt_hr_fingerscan", indexes={@ORM\Index(name="nmt_hr_fingerscan_KF1_idx", columns={"created_by"}), @ORM\Index(name="nmt_hr_fingerscan_KF2_idx", columns={"employee_code"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Repository\NmtHrFingerscanRepository")
 
 */
class NmtHrFingerscan
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
     * @ORM\Column(name="employee_id", type="integer", nullable=false)
     */
    private $employeeId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="employee_code", type="string", length=10, nullable=false)
     */
    private $employeeCode;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="attendance_date", type="datetime", nullable=true)
     */
    private $attendanceDate;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="attendance_type", type="integer", nullable=true)
     */
    private $attendanceType;
    
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
     * @var string
     *
     * @ORM\Column(name="clock_in", type="string", length=10, nullable=true)
     */
    private $clockIn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="clock_out", type="string", length=10, nullable=true)
     */
    private $clockOut;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reconciled_on", type="datetime", nullable=true)
     */
    private $reconciledOn;
    
    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
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
     * Set employeeId
     *
     * @param integer $employeeId
     *
     * @return NmtHrFingerscan
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
        
        return $this;
    }
    
    /**
     * Get employeeId
     *
     * @return integer
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }
    
    /**
     * Set employeeCode
     *
     * @param string $employeeCode
     *
     * @return NmtHrFingerscan
     */
    public function setEmployeeCode($employeeCode)
    {
        $this->employeeCode = $employeeCode;
        
        return $this;
    }
    
    /**
     * Get employeeCode
     *
     * @return string
     */
    public function getEmployeeCode()
    {
        return $this->employeeCode;
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
     * @param string $clockIn
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
     * @return string
     */
    public function getClockIn()
    {
        return $this->clockIn;
    }
    
    /**
     * Set clockOut
     *
     * @param string $clockOut
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
     * @return string
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
     * Set reconciledOn
     *
     * @param \DateTime $reconciledOn
     *
     * @return NmtHrFingerscan
     */
    public function setReconciledOn($reconciledOn)
    {
        $this->reconciledOn = $reconciledOn;
        
        return $this;
    }
    
    /**
     * Get reconciledOn
     *
     * @return \DateTime
     */
    public function getReconciledOn()
    {
        return $this->reconciledOn;
    }
    
    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtHrFingerscan
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
     * @return NmtHrFingerscan
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
