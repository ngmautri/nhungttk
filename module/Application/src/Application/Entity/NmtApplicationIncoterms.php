<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationIncoterms
 *
 * @ORM\Table(name="nmt_application_incoterms", indexes={@ORM\Index(name="nmt_application_incoterms_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_incoterms_FK2_idx", columns={"lastchange_by"})})
 * @ORM\Entity
 */
class NmtApplicationIncoterms
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
     * @ORM\Column(name="incoterm", type="string", length=3, nullable=false)
     */
    private $incoterm;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm_description", type="text", length=65535, nullable=true)
     */
    private $incotermDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="location_required", type="boolean", nullable=true)
     */
    private $locationRequired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revisionNo", type="integer", nullable=true)
     */
    private $revisionno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm1", type="string", length=55, nullable=true)
     */
    private $incoterm1;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;



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
     * Set incoterm
     *
     * @param string $incoterm
     *
     * @return NmtApplicationIncoterms
     */
    public function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;

        return $this;
    }

    /**
     * Get incoterm
     *
     * @return string
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * Set incotermDescription
     *
     * @param string $incotermDescription
     *
     * @return NmtApplicationIncoterms
     */
    public function setIncotermDescription($incotermDescription)
    {
        $this->incotermDescription = $incotermDescription;

        return $this;
    }

    /**
     * Get incotermDescription
     *
     * @return string
     */
    public function getIncotermDescription()
    {
        return $this->incotermDescription;
    }

    /**
     * Set locationRequired
     *
     * @param boolean $locationRequired
     *
     * @return NmtApplicationIncoterms
     */
    public function setLocationRequired($locationRequired)
    {
        $this->locationRequired = $locationRequired;

        return $this;
    }

    /**
     * Get locationRequired
     *
     * @return boolean
     */
    public function getLocationRequired()
    {
        return $this->locationRequired;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationIncoterms
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
     * Set revisionno
     *
     * @param integer $revisionno
     *
     * @return NmtApplicationIncoterms
     */
    public function setRevisionno($revisionno)
    {
        $this->revisionno = $revisionno;

        return $this;
    }

    /**
     * Get revisionno
     *
     * @return integer
     */
    public function getRevisionno()
    {
        return $this->revisionno;
    }

    /**
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtApplicationIncoterms
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set incoterm1
     *
     * @param string $incoterm1
     *
     * @return NmtApplicationIncoterms
     */
    public function setIncoterm1($incoterm1)
    {
        $this->incoterm1 = $incoterm1;

        return $this;
    }

    /**
     * Get incoterm1
     *
     * @return string
     */
    public function getIncoterm1()
    {
        return $this->incoterm1;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtApplicationIncoterms
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationIncoterms
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
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return NmtApplicationIncoterms
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }
}
