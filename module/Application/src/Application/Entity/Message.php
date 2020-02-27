<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity
 */
class Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
     * @return Message
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
}
