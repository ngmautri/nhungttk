<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InventoryMessage
 *
 * @ORM\Table(name="inventory_message")
 * @ORM\Entity
 */
class InventoryMessage
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
     * @ORM\Column(name="uuid", type="string", length=38, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="msg_header", type="string", length=45, nullable=true)
     */
    private $msgHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="msg_body", type="text", nullable=true)
     */
    private $msgBody;

    /**
     * @var string
     *
     * @ORM\Column(name="queue_name", type="string", length=100, nullable=true)
     */
    private $queueName;

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
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="messagecol", type="string", length=45, nullable=true)
     */
    private $messagecol;

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
     * @ORM\Column(name="event_name", type="string", length=255, nullable=true)
     */
    private $eventName;



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
     * @return InventoryMessage
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
     * @return InventoryMessage
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
     * @return InventoryMessage
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
     * Set queueName
     *
     * @param string $queueName
     *
     * @return InventoryMessage
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return InventoryMessage
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
     * @return InventoryMessage
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return InventoryMessage
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
     * Set messagecol
     *
     * @param string $messagecol
     *
     * @return InventoryMessage
     */
    public function setMessagecol($messagecol)
    {
        $this->messagecol = $messagecol;

        return $this;
    }

    /**
     * Get messagecol
     *
     * @return string
     */
    public function getMessagecol()
    {
        return $this->messagecol;
    }

    /**
     * Set sentOn
     *
     * @param \DateTime $sentOn
     *
     * @return InventoryMessage
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
     * @return InventoryMessage
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
     * Set eventName
     *
     * @param string $eventName
     *
     * @return InventoryMessage
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
}
