<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PmtLog
 *
 * @ORM\Table(name="pmt_log", indexes={@ORM\Index(name="pmt_log_FK1_idx", columns={"created_by"}), @ORM\Index(name="pmt_log_FK2_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class PmtLog
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
     * @var string
     *
     * @ORM\Column(name="event_name", type="string", length=100, nullable=true)
     */
    private $eventName;

    /**
     * @var integer
     *
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_class", type="string", length=150, nullable=true)
     */
    private $entityClass;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_token", type="string", length=45, nullable=true)
     */
    private $entityToken;

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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * @return PmtLog
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
     * Set eventName
     *
     * @param string $eventName
     *
     * @return PmtLog
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * Get eventName
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     *
     * @return PmtLog
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entityClass
     *
     * @param string $entityClass
     *
     * @return PmtLog
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get entityClass
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set entityToken
     *
     * @param string $entityToken
     *
     * @return PmtLog
     */
    public function setEntityToken($entityToken)
    {
        $this->entityToken = $entityToken;

        return $this;
    }

    /**
     * Get entityToken
     *
     * @return string
     */
    public function getEntityToken()
    {
        return $this->entityToken;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return PmtLog
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return PmtLog
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
