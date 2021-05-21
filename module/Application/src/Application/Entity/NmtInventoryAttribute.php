<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryAttribute
 *
 * @ORM\Table(name="nmt_inventory_attribute", indexes={@ORM\Index(name="nmt_inventory_attribute_FK01_idx", columns={"group_id"}), @ORM\Index(name="nmt_inventory_attribute_FK02_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_attribute_FK03_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtInventoryAttribute
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
     * @ORM\Column(name="attribute_code", type="string", length=45, nullable=true)
     */
    private $attributeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute_name", type="string", length=45, nullable=true)
     */
    private $attributeName;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute_name_1", type="string", length=45, nullable=true)
     */
    private $attributeName1;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute_name_2", type="string", length=45, nullable=true)
     */
    private $attributeName2;

    /**
     * @var string
     *
     * @ORM\Column(name="combined_name", type="string", length=128, nullable=true)
     */
    private $combinedName;

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
     * @var integer
     *
     * @ORM\Column(name="sys_number", type="integer", nullable=true)
     */
    private $sysNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=128, nullable=true)
     */
    private $remarks;

    /**
     * @var \Application\Entity\NmtInventoryAttributeGroup
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryAttributeGroup")
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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtInventoryAttribute
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
     * Set attributeCode
     *
     * @param string $attributeCode
     *
     * @return NmtInventoryAttribute
     */
    public function setAttributeCode($attributeCode)
    {
        $this->attributeCode = $attributeCode;

        return $this;
    }

    /**
     * Get attributeCode
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * Set attributeName
     *
     * @param string $attributeName
     *
     * @return NmtInventoryAttribute
     */
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;

        return $this;
    }

    /**
     * Get attributeName
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * Set attributeName1
     *
     * @param string $attributeName1
     *
     * @return NmtInventoryAttribute
     */
    public function setAttributeName1($attributeName1)
    {
        $this->attributeName1 = $attributeName1;

        return $this;
    }

    /**
     * Get attributeName1
     *
     * @return string
     */
    public function getAttributeName1()
    {
        return $this->attributeName1;
    }

    /**
     * Set attributeName2
     *
     * @param string $attributeName2
     *
     * @return NmtInventoryAttribute
     */
    public function setAttributeName2($attributeName2)
    {
        $this->attributeName2 = $attributeName2;

        return $this;
    }

    /**
     * Get attributeName2
     *
     * @return string
     */
    public function getAttributeName2()
    {
        return $this->attributeName2;
    }

    /**
     * Set combinedName
     *
     * @param string $combinedName
     *
     * @return NmtInventoryAttribute
     */
    public function setCombinedName($combinedName)
    {
        $this->combinedName = $combinedName;

        return $this;
    }

    /**
     * Get combinedName
     *
     * @return string
     */
    public function getCombinedName()
    {
        return $this->combinedName;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryAttribute
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
     * @return NmtInventoryAttribute
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
     * Set sysNumber
     *
     * @param integer $sysNumber
     *
     * @return NmtInventoryAttribute
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;

        return $this;
    }

    /**
     * Get sysNumber
     *
     * @return integer
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * Set version
     *
     * @param integer $version
     *
     * @return NmtInventoryAttribute
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryAttribute
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryAttribute
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
     * Set group
     *
     * @param \Application\Entity\NmtInventoryAttributeGroup $group
     *
     * @return NmtInventoryAttribute
     */
    public function setGroup(\Application\Entity\NmtInventoryAttributeGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Application\Entity\NmtInventoryAttributeGroup
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
     * @return NmtInventoryAttribute
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
     * @return NmtInventoryAttribute
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
