<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationPmtTerm
 *
 * @ORM\Table(name="nmt_application_pmt_term", indexes={@ORM\Index(name="nmt_application_pmt_term_FK1_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtApplicationPmtTerm
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
     * @ORM\Column(name="pmt_term_code", type="string", length=45, nullable=true)
     */
    private $pmtTermCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="pmt_term_name", type="integer", nullable=true)
     */
    private $pmtTermName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

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
     * Set pmtTermCode
     *
     * @param string $pmtTermCode
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPmtTermCode($pmtTermCode)
    {
        $this->pmtTermCode = $pmtTermCode;

        return $this;
    }

    /**
     * Get pmtTermCode
     *
     * @return string
     */
    public function getPmtTermCode()
    {
        return $this->pmtTermCode;
    }

    /**
     * Set pmtTermName
     *
     * @param integer $pmtTermName
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPmtTermName($pmtTermName)
    {
        $this->pmtTermName = $pmtTermName;

        return $this;
    }

    /**
     * Get pmtTermName
     *
     * @return integer
     */
    public function getPmtTermName()
    {
        return $this->pmtTermName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtApplicationPmtTerm
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
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationPmtTerm
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
     * @return NmtApplicationPmtTerm
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
     * @return NmtApplicationPmtTerm
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
