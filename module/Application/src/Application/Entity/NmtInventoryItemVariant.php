<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemVariant
 *
 * @ORM\Table(name="nmt_inventory_item_variant", indexes={@ORM\Index(name="nmt_inventory_item_variant_FK1_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_variant_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_variant_FK3_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtInventoryItemVariant
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
     * @ORM\Column(name="variant_name", type="string", length=100, nullable=true)
     */
    private $variantName;

    /**
     * @var string
     *
     * @ORM\Column(name="varriant_description", type="string", length=255, nullable=true)
     */
    private $varriantDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_text", type="text", length=65535, nullable=true)
     */
    private $variantText;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_colour", type="string", length=45, nullable=true)
     */
    private $variantColour;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_size", type="string", length=45, nullable=true)
     */
    private $variantSize;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_origin", type="string", length=45, nullable=true)
     */
    private $variantOrigin;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute1", type="string", length=45, nullable=true)
     */
    private $attribute1;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute2", type="string", length=45, nullable=true)
     */
    private $attribute2;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute3", type="string", length=45, nullable=true)
     */
    private $attribute3;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute4", type="string", length=45, nullable=true)
     */
    private $attribute4;

    /**
     * @var string
     *
     * @ORM\Column(name="attribute5", type="string", length=45, nullable=true)
     */
    private $attribute5;

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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryItemVariant
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItemVariant
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
     * @return NmtInventoryItemVariant
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
     * Set variantName
     *
     * @param string $variantName
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantName($variantName)
    {
        $this->variantName = $variantName;

        return $this;
    }

    /**
     * Get variantName
     *
     * @return string
     */
    public function getVariantName()
    {
        return $this->variantName;
    }

    /**
     * Set varriantDescription
     *
     * @param string $varriantDescription
     *
     * @return NmtInventoryItemVariant
     */
    public function setVarriantDescription($varriantDescription)
    {
        $this->varriantDescription = $varriantDescription;

        return $this;
    }

    /**
     * Get varriantDescription
     *
     * @return string
     */
    public function getVarriantDescription()
    {
        return $this->varriantDescription;
    }

    /**
     * Set variantText
     *
     * @param string $variantText
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantText($variantText)
    {
        $this->variantText = $variantText;

        return $this;
    }

    /**
     * Get variantText
     *
     * @return string
     */
    public function getVariantText()
    {
        return $this->variantText;
    }

    /**
     * Set variantColour
     *
     * @param string $variantColour
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantColour($variantColour)
    {
        $this->variantColour = $variantColour;

        return $this;
    }

    /**
     * Get variantColour
     *
     * @return string
     */
    public function getVariantColour()
    {
        return $this->variantColour;
    }

    /**
     * Set variantSize
     *
     * @param string $variantSize
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantSize($variantSize)
    {
        $this->variantSize = $variantSize;

        return $this;
    }

    /**
     * Get variantSize
     *
     * @return string
     */
    public function getVariantSize()
    {
        return $this->variantSize;
    }

    /**
     * Set variantOrigin
     *
     * @param string $variantOrigin
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantOrigin($variantOrigin)
    {
        $this->variantOrigin = $variantOrigin;

        return $this;
    }

    /**
     * Get variantOrigin
     *
     * @return string
     */
    public function getVariantOrigin()
    {
        return $this->variantOrigin;
    }

    /**
     * Set attribute1
     *
     * @param string $attribute1
     *
     * @return NmtInventoryItemVariant
     */
    public function setAttribute1($attribute1)
    {
        $this->attribute1 = $attribute1;

        return $this;
    }

    /**
     * Get attribute1
     *
     * @return string
     */
    public function getAttribute1()
    {
        return $this->attribute1;
    }

    /**
     * Set attribute2
     *
     * @param string $attribute2
     *
     * @return NmtInventoryItemVariant
     */
    public function setAttribute2($attribute2)
    {
        $this->attribute2 = $attribute2;

        return $this;
    }

    /**
     * Get attribute2
     *
     * @return string
     */
    public function getAttribute2()
    {
        return $this->attribute2;
    }

    /**
     * Set attribute3
     *
     * @param string $attribute3
     *
     * @return NmtInventoryItemVariant
     */
    public function setAttribute3($attribute3)
    {
        $this->attribute3 = $attribute3;

        return $this;
    }

    /**
     * Get attribute3
     *
     * @return string
     */
    public function getAttribute3()
    {
        return $this->attribute3;
    }

    /**
     * Set attribute4
     *
     * @param string $attribute4
     *
     * @return NmtInventoryItemVariant
     */
    public function setAttribute4($attribute4)
    {
        $this->attribute4 = $attribute4;

        return $this;
    }

    /**
     * Get attribute4
     *
     * @return string
     */
    public function getAttribute4()
    {
        return $this->attribute4;
    }

    /**
     * Set attribute5
     *
     * @param string $attribute5
     *
     * @return NmtInventoryItemVariant
     */
    public function setAttribute5($attribute5)
    {
        $this->attribute5 = $attribute5;

        return $this;
    }

    /**
     * Get attribute5
     *
     * @return string
     */
    public function getAttribute5()
    {
        return $this->attribute5;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemVariant
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

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemVariant
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
     * @return NmtInventoryItemVariant
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
