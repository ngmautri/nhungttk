<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationDeliveryTerm
 *
 * @ORM\Table(name="nmt_application_delivery_term", uniqueConstraints={@ORM\UniqueConstraint(name="delivery_term_code_UNIQUE", columns={"delivery_term_code"})}, indexes={@ORM\Index(name="nmt_application_delivery_term_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtApplicationDeliveryTerm
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
     * @ORM\Column(name="delivery_term_code", type="string", length=45, nullable=false)
     */
    private $deliveryTermCode;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_term_name", type="string", length=45, nullable=false)
     */
    private $deliveryTermName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="icoterm", type="string", length=45, nullable=true)
     */
    private $icoterm;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     * Set deliveryTermCode
     *
     * @param string $deliveryTermCode
     *
     * @return NmtApplicationDeliveryTerm
     */
    public function setDeliveryTermCode($deliveryTermCode)
    {
        $this->deliveryTermCode = $deliveryTermCode;

        return $this;
    }

    /**
     * Get deliveryTermCode
     *
     * @return string
     */
    public function getDeliveryTermCode()
    {
        return $this->deliveryTermCode;
    }

    /**
     * Set deliveryTermName
     *
     * @param string $deliveryTermName
     *
     * @return NmtApplicationDeliveryTerm
     */
    public function setDeliveryTermName($deliveryTermName)
    {
        $this->deliveryTermName = $deliveryTermName;

        return $this;
    }

    /**
     * Get deliveryTermName
     *
     * @return string
     */
    public function getDeliveryTermName()
    {
        return $this->deliveryTermName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtApplicationDeliveryTerm
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
     * Set icoterm
     *
     * @param string $icoterm
     *
     * @return NmtApplicationDeliveryTerm
     */
    public function setIcoterm($icoterm)
    {
        $this->icoterm = $icoterm;

        return $this;
    }

    /**
     * Get icoterm
     *
     * @return string
     */
    public function getIcoterm()
    {
        return $this->icoterm;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationDeliveryTerm
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationDeliveryTerm
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
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationDeliveryTerm
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
