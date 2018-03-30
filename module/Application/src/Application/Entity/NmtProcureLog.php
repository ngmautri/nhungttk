<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureLog
 *
 * @ORM\Table(name="nmt_procure_log", indexes={@ORM\Index(name="nmt_procure_log_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtProcureLog
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="triggeredBy", type="string", length=255, nullable=true)
     */
    private $triggeredby;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="priority_name", type="string", length=45, nullable=true)
     */
    private $priorityName;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=true)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_info", type="text", length=65535, nullable=true)
     */
    private $extraInfo;

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
     * @return NmtProcureLog
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtProcureLog
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
     * @return NmtProcureLog
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
     * Set triggeredby
     *
     * @param string $triggeredby
     *
     * @return NmtProcureLog
     */
    public function setTriggeredby($triggeredby)
    {
        $this->triggeredby = $triggeredby;

        return $this;
    }

    /**
     * Get triggeredby
     *
     * @return string
     */
    public function getTriggeredby()
    {
        return $this->triggeredby;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return NmtProcureLog
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priorityName
     *
     * @param string $priorityName
     *
     * @return NmtProcureLog
     */
    public function setPriorityName($priorityName)
    {
        $this->priorityName = $priorityName;

        return $this;
    }

    /**
     * Get priorityName
     *
     * @return string
     */
    public function getPriorityName()
    {
        return $this->priorityName;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return NmtProcureLog
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set extraInfo
     *
     * @param string $extraInfo
     *
     * @return NmtProcureLog
     */
    public function setExtraInfo($extraInfo)
    {
        $this->extraInfo = $extraInfo;

        return $this;
    }

    /**
     * Get extraInfo
     *
     * @return string
     */
    public function getExtraInfo()
    {
        return $this->extraInfo;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcureLog
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
