<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaDelivery
 *
 * @ORM\Table(name="mla_delivery", indexes={@ORM\Index(name="mla_delivery_FK1_idx", columns={"created_by"}), @ORM\Index(name="mla_delivery_FK1_idx1", columns={"last_workflow_id"})})
 * @ORM\Entity
 */
class MlaDelivery
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
     * @ORM\Column(name="dn_number", type="string", length=45, nullable=true)
     */
    private $dnNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_workflow_id", type="integer", nullable=true)
     */
    private $lastWorkflowId;

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
     * Set dnNumber
     *
     * @param string $dnNumber
     *
     * @return MlaDelivery
     */
    public function setDnNumber($dnNumber)
    {
        $this->dnNumber = $dnNumber;

        return $this;
    }

    /**
     * Get dnNumber
     *
     * @return string
     */
    public function getDnNumber()
    {
        return $this->dnNumber;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MlaDelivery
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MlaDelivery
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
     * Set lastWorkflowId
     *
     * @param integer $lastWorkflowId
     *
     * @return MlaDelivery
     */
    public function setLastWorkflowId($lastWorkflowId)
    {
        $this->lastWorkflowId = $lastWorkflowId;

        return $this;
    }

    /**
     * Get lastWorkflowId
     *
     * @return integer
     */
    public function getLastWorkflowId()
    {
        return $this->lastWorkflowId;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return MlaDelivery
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
