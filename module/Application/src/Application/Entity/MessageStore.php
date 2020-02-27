<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageStore
 *
 * @ORM\Table(name="message_store")
 * @ORM\Entity
 */
class MessageStore
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
     * @ORM\Column(name="uuid", type="string", length=45, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="msg_header", type="string", length=255, nullable=true)
     */
    private $msgHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="msg_body", type="text", nullable=true)
     */
    private $msgBody;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="available_on", type="datetime", nullable=true)
     */
    private $availableOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expired_on", type="datetime", nullable=true)
     */
    private $expiredOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_on", type="datetime", nullable=true)
     */
    private $sentOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="consumed_on", type="datetime", nullable=true)
     */
    private $consumedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="queue_name", type="string", length=100, nullable=true)
     */
    private $queueName;

    /**
     * @var string
     *
     * @ORM\Column(name="event_name", type="string", length=255, nullable=true)
     */
    private $eventName;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=255, nullable=true)
     */
    private $className;

    /**
     * @var string
     *
     * @ORM\Column(name="triggered_by", type="string", length=255, nullable=true)
     */
    private $triggeredBy;



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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return MessageStore
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set msgHeader
     *
     * @param string $msgHeader
     *
     * @return MessageStore
     */
    public function setMsgHeader($msgHeader)
    {
        $this->msgHeader = $msgHeader;

        return $this;
    }

    /**
     * Get msgHeader
     *
     * @return string
     */
    public function getMsgHeader()
    {
        return $this->msgHeader;
    }

    /**
     * Set msgBody
     *
     * @param string $msgBody
     *
     * @return MessageStore
     */
    public function setMsgBody($msgBody)
    {
        $this->msgBody = $msgBody;

        return $this;
    }

    /**
     * Get msgBody
     *
     * @return string
     */
    public function getMsgBody()
    {
        return $this->msgBody;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MessageStore
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
     * Set availableOn
     *
     * @param \DateTime $availableOn
     *
     * @return MessageStore
     */
    public function setAvailableOn($availableOn)
    {
        $this->availableOn = $availableOn;

        return $this;
    }

    /**
     * Get availableOn
     *
     * @return \DateTime
     */
    public function getAvailableOn()
    {
        return $this->availableOn;
    }

    /**
     * Set expiredOn
     *
     * @param \DateTime $expiredOn
     *
     * @return MessageStore
     */
    public function setExpiredOn($expiredOn)
    {
        $this->expiredOn = $expiredOn;

        return $this;
    }

    /**
     * Get expiredOn
     *
     * @return \DateTime
     */
    public function getExpiredOn()
    {
        return $this->expiredOn;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return MessageStore
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
     * Set sentOn
     *
     * @param \DateTime $sentOn
     *
     * @return MessageStore
     */
    public function setSentOn($sentOn)
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * Get sentOn
     *
     * @return \DateTime
     */
    public function getSentOn()
    {
        return $this->sentOn;
    }

    /**
     * Set consumedOn
     *
     * @param \DateTime $consumedOn
     *
     * @return MessageStore
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
     * Set queueName
     *
     * @param string $queueName
     *
     * @return MessageStore
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * Get queueName
     *
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * Set eventName
     *
     * @param string $eventName
     *
     * @return MessageStore
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return MessageStore
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
     * Set className
     *
     * @param string $className
     *
     * @return MessageStore
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set triggeredBy
     *
     * @param string $triggeredBy
     *
     * @return MessageStore
     */
    public function setTriggeredBy($triggeredBy)
    {
        $this->triggeredBy = $triggeredBy;

        return $this;
    }

    /**
     * Get triggeredBy
     *
     * @return string
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }
}
