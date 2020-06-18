<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryAssociationItem
 *
 * @ORM\Table(name="nmt_inventory_association_item", indexes={@ORM\Index(name="nmt_inventory_association_item_FK1_idx", columns={"association_id"}), @ORM\Index(name="nmt_inventory_association_item_FK2_idx", columns={"main_item_id"}), @ORM\Index(name="nmt_inventory_association_item_FK3_idx", columns={"related_item_id"}), @ORM\Index(name="nmt_inventory_association_item_FK3_idx1", columns={"created_by"}), @ORM\Index(name="nmt_inventory_association_item_FK5_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_association_item_FK6_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtInventoryAssociationItem
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
     * @ORM\Column(name="uuid", type="integer", nullable=true)
     */
    private $uuid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_both_direction", type="boolean", nullable=true)
     */
    private $hasBothDirection;

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
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="version", type="integer", nullable=true)
     */
    private $version;

    /**
     * @var \Application\Entity\NmtInventoryAssociation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryAssociation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="association_id", referencedColumnName="id")
     * })
     */
    private $association;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="main_item_id", referencedColumnName="id")
     * })
     */
    private $mainItem;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="related_item_id", referencedColumnName="id")
     * })
     */
    private $relatedItem;

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
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;



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
     * @param integer $uuid
     *
     * @return NmtInventoryAssociationItem
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return integer
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryAssociationItem
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
     * Set hasBothDirection
     *
     * @param boolean $hasBothDirection
     *
     * @return NmtInventoryAssociationItem
     */
    public function setHasBothDirection($hasBothDirection)
    {
        $this->hasBothDirection = $hasBothDirection;

        return $this;
    }

    /**
     * Get hasBothDirection
     *
     * @return boolean
     */
    public function getHasBothDirection()
    {
        return $this->hasBothDirection;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryAssociationItem
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
     * @return NmtInventoryAssociationItem
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryAssociationItem
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryAssociationItem
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
     * Set version
     *
     * @param integer $version
     *
     * @return NmtInventoryAssociationItem
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
     * Set association
     *
     * @param \Application\Entity\NmtInventoryAssociation $association
     *
     * @return NmtInventoryAssociationItem
     */
    public function setAssociation(\Application\Entity\NmtInventoryAssociation $association = null)
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association
     *
     * @return \Application\Entity\NmtInventoryAssociation
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Set mainItem
     *
     * @param \Application\Entity\NmtInventoryItem $mainItem
     *
     * @return NmtInventoryAssociationItem
     */
    public function setMainItem(\Application\Entity\NmtInventoryItem $mainItem = null)
    {
        $this->mainItem = $mainItem;

        return $this;
    }

    /**
     * Get mainItem
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getMainItem()
    {
        return $this->mainItem;
    }

    /**
     * Set relatedItem
     *
     * @param \Application\Entity\NmtInventoryItem $relatedItem
     *
     * @return NmtInventoryAssociationItem
     */
    public function setRelatedItem(\Application\Entity\NmtInventoryItem $relatedItem = null)
    {
        $this->relatedItem = $relatedItem;

        return $this;
    }

    /**
     * Get relatedItem
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getRelatedItem()
    {
        return $this->relatedItem;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryAssociationItem
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
     * @return NmtInventoryAssociationItem
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

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtInventoryAssociationItem
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }
}
