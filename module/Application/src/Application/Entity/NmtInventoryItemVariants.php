<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemVariants
 *
 * @ORM\Table(name="nmt_inventory_item_variants", indexes={@ORM\Index(name="nmt_inventory_item_variants_FK01_idx", columns={"item_attribute_id"}), @ORM\Index(name="nmt_inventory_item_variants_FK02_idx", columns={"attribute_id"})})
 * @ORM\Entity
 */
class NmtInventoryItemVariants
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
     * @var \Application\Entity\NmtInventoryItemAttribute
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemAttribute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_attribute_id", referencedColumnName="id")
     * })
     */
    private $itemAttribute;

    /**
     * @var \Application\Entity\NmtInventoryAttribute
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryAttribute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     * })
     */
    private $attribute;



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
     * @return NmtInventoryItemVariants
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
     * Set itemAttribute
     *
     * @param \Application\Entity\NmtInventoryItemAttribute $itemAttribute
     *
     * @return NmtInventoryItemVariants
     */
    public function setItemAttribute(\Application\Entity\NmtInventoryItemAttribute $itemAttribute = null)
    {
        $this->itemAttribute = $itemAttribute;

        return $this;
    }

    /**
     * Get itemAttribute
     *
     * @return \Application\Entity\NmtInventoryItemAttribute
     */
    public function getItemAttribute()
    {
        return $this->itemAttribute;
    }

    /**
     * Set attribute
     *
     * @param \Application\Entity\NmtInventoryAttribute $attribute
     *
     * @return NmtInventoryItemVariants
     */
    public function setAttribute(\Application\Entity\NmtInventoryAttribute $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \Application\Entity\NmtInventoryAttribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
