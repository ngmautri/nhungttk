<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemComponent
 *
 * @ORM\Table(name="nmt_inventory_item_component", indexes={@ORM\Index(name="nmt_inventory_item_component_idx01", columns={"uuid"}), @ORM\Index(name="nmt_inventory_item_component_FK01_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_component_FK02_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_item_component_FK03_idx", columns={"parent_id"}), @ORM\Index(name="nmt_inventory_item_component_FK04_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class NmtInventoryItemComponent
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=45, nullable=true)
     */
    private $uuid;

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
     * @ORM\Column(name="quantity", type="decimal", precision=18, scale=6, nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="uom", type="string", length=45, nullable=true)
     */
    private $uom;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=18, scale=6, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=100, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_member", type="boolean", nullable=true)
     */
    private $hasMember;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_uuid", type="string", length=45, nullable=true)
     */
    private $parentUuid;

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
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;



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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryItemComponent
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtInventoryItemComponent
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItemComponent
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
     * @return NmtInventoryItemComponent
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
     * Set quantity
     *
     * @param string $quantity
     *
     * @return NmtInventoryItemComponent
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set uom
     *
     * @param string $uom
     *
     * @return NmtInventoryItemComponent
     */
    public function setUom($uom)
    {
        $this->uom = $uom;

        return $this;
    }

    /**
     * Get uom
     *
     * @return string
     */
    public function getUom()
    {
        return $this->uom;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return NmtInventoryItemComponent
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemComponent
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
     * Set hasMember
     *
     * @param boolean $hasMember
     *
     * @return NmtInventoryItemComponent
     */
    public function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;

        return $this;
    }

    /**
     * Get hasMember
     *
     * @return boolean
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     * Set parentUuid
     *
     * @param string $parentUuid
     *
     * @return NmtInventoryItemComponent
     */
    public function setParentUuid($parentUuid)
    {
        $this->parentUuid = $parentUuid;

        return $this;
    }

    /**
     * Get parentUuid
     *
     * @return string
     */
    public function getParentUuid()
    {
        return $this->parentUuid;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryItemComponent
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
     * @return NmtInventoryItemComponent
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemComponent
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
     * @return NmtInventoryItemComponent
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
     * Set parent
     *
     * @param \Application\Entity\NmtInventoryItem $parent
     *
     * @return NmtInventoryItemComponent
     */
    public function setParent(\Application\Entity\NmtInventoryItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemComponent
     */
    public function setItem(\Application\Entity\NmtInventoryItem $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getItem()
    {
        return $this->item;
    }
}
