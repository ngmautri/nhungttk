<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppUomGroupMember
 *
 * @ORM\Table(name="app_uom_group_member", uniqueConstraints={@ORM\UniqueConstraint(name="idtable1_UNIQUE", columns={"id"})}, indexes={@ORM\Index(name="app_uom_group_member_FK1_idx", columns={"group_id"}), @ORM\Index(name="app_uom_group_member_FK2_idx", columns={"uom_id"}), @ORM\Index(name="app_uom_group_member_FK3_idx", columns={"base_uom_id"}), @ORM\Index(name="app_uom_group_member_FK4_idx", columns={"created_by"}), @ORM\Index(name="app_uom_group_member_FK5_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class AppUomGroupMember
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
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_change_on", type="integer", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var \Application\Entity\AppUomGroup
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\AppUomGroup")
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
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;



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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return AppUomGroupMember
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return AppUomGroupMember
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return AppUomGroupMember
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
     * Set lastChangeOn
     *
     * @param integer $lastChangeOn
     *
     * @return AppUomGroupMember
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return integer
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set group
     *
     * @param \Application\Entity\AppUomGroup $group
     *
     * @return AppUomGroupMember
     */
    public function setGroup(\Application\Entity\AppUomGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Application\Entity\AppUomGroup
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
     * @return AppUomGroupMember
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
     * @return AppUomGroupMember
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

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return AppUomGroupMember
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return AppUomGroupMember
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }
}
