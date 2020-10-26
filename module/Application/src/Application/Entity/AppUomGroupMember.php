<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppUomGroupMember
 *
 * @ORM\Table(name="app_uom_group_member", uniqueConstraints={@ORM\UniqueConstraint(name="idtable1_UNIQUE", columns={"id"})}, indexes={@ORM\Index(name="app_uom_group_member_FK1_idx", columns={"group_id"}), @ORM\Index(name="app_uom_group_member_FK4_idx", columns={"created_by"}), @ORM\Index(name="app_uom_group_member_FK5_idx", columns={"last_change_by"})})
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
     * @var integer
     *
     * @ORM\Column(name="uom_id", type="integer", nullable=true)
     */
    private $uomId;

    /**
     * @var integer
     *
     * @ORM\Column(name="base_uom_id", type="integer", nullable=true)
     */
    private $baseUomId;

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
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="counter_uom", type="string", length=45, nullable=true)
     */
    private $counterUom;

    /**
     * @var string
     *
     * @ORM\Column(name="convert_factor", type="decimal", precision=15, scale=0, nullable=true)
     */
    private $convertFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=45, nullable=true)
     */
    private $groupName;

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
     * Set uomId
     *
     * @param integer $uomId
     *
     * @return AppUomGroupMember
     */
    public function setUomId($uomId)
    {
        $this->uomId = $uomId;

        return $this;
    }

    /**
     * Get uomId
     *
     * @return integer
     */
    public function getUomId()
    {
        return $this->uomId;
    }

    /**
     * Set baseUomId
     *
     * @param integer $baseUomId
     *
     * @return AppUomGroupMember
     */
    public function setBaseUomId($baseUomId)
    {
        $this->baseUomId = $baseUomId;

        return $this;
    }

    /**
     * Get baseUomId
     *
     * @return integer
     */
    public function getBaseUomId()
    {
        return $this->baseUomId;
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
     * @param \DateTime $lastChangeOn
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
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set counterUom
     *
     * @param string $counterUom
     *
     * @return AppUomGroupMember
     */
    public function setCounterUom($counterUom)
    {
        $this->counterUom = $counterUom;

        return $this;
    }

    /**
     * Get counterUom
     *
     * @return string
     */
    public function getCounterUom()
    {
        return $this->counterUom;
    }

    /**
     * Set convertFactor
     *
     * @param string $convertFactor
     *
     * @return AppUomGroupMember
     */
    public function setConvertFactor($convertFactor)
    {
        $this->convertFactor = $convertFactor;

        return $this;
    }

    /**
     * Get convertFactor
     *
     * @return string
     */
    public function getConvertFactor()
    {
        return $this->convertFactor;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return AppUomGroupMember
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
     * Set groupName
     *
     * @param string $groupName
     *
     * @return AppUomGroupMember
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
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
