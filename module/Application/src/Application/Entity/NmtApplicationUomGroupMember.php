<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationUomGroupMember
 *
 * @ORM\Table(name="nmt_application_uom_group_member", indexes={@ORM\Index(name="nmt_application_uom_group_member_FK1_idx", columns={"group_id"}), @ORM\Index(name="nmt_application_uom_group_member_FK2_idx", columns={"uom_id"}), @ORM\Index(name="nmt_application_uom_group_member_FK3_idx", columns={"base_uom_id"})})
 * @ORM\Entity
 */
class NmtApplicationUomGroupMember
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
     * @var integer
     *
     * @ORM\Column(name="convert_factor", type="integer", nullable=false)
     */
    private $convertFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \Application\Entity\NmtApplicationUomGroup
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUomGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uom_id", referencedColumnName="id")
     * })
     */
    private $uom;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="base_uom_id", referencedColumnName="id")
     * })
     */
    private $baseUom;



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
     * @return NmtApplicationUomGroupMember
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
     * Set convertFactor
     *
     * @param integer $convertFactor
     *
     * @return NmtApplicationUomGroupMember
     */
    public function setConvertFactor($convertFactor)
    {
        $this->convertFactor = $convertFactor;

        return $this;
    }

    /**
     * Get convertFactor
     *
     * @return integer
     */
    public function getConvertFactor()
    {
        return $this->convertFactor;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtApplicationUomGroupMember
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
     * @param integer $createdBy
     *
     * @return NmtApplicationUomGroupMember
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationUomGroupMember
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationUomGroupMember
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set group
     *
     * @param \Application\Entity\NmtApplicationUomGroup $group
     *
     * @return NmtApplicationUomGroupMember
     */
    public function setGroup(\Application\Entity\NmtApplicationUomGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Application\Entity\NmtApplicationUomGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set uom
     *
     * @param \Application\Entity\NmtApplicationUom $uom
     *
     * @return NmtApplicationUomGroupMember
     */
    public function setUom(\Application\Entity\NmtApplicationUom $uom = null)
    {
        $this->uom = $uom;

        return $this;
    }

    /**
     * Get uom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getUom()
    {
        return $this->uom;
    }

    /**
     * Set baseUom
     *
     * @param \Application\Entity\NmtApplicationUom $baseUom
     *
     * @return NmtApplicationUomGroupMember
     */
    public function setBaseUom(\Application\Entity\NmtApplicationUom $baseUom = null)
    {
        $this->baseUom = $baseUom;

        return $this;
    }

    /**
     * Get baseUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getBaseUom()
    {
        return $this->baseUom;
    }
}
