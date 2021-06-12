<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemVariant
 *
 * @ORM\Table(name="nmt_inventory_item_variant", indexes={@ORM\Index(name="nmt_inventory_item_variant_FK02_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_variant_FK03_idx", columns={"last_change_by"}), @ORM\Index(name="item_id", columns={"item_id"})})
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
     * @ORM\Column(name="uuid", type="string", length=45, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="combined_name", type="string", length=200, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=6, nullable=true)
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=45, nullable=true)
     */
    private $upc;

    /**
     * @var string
     *
     * @ORM\Column(name="ean13", type="string", length=45, nullable=true)
     */
    private $ean13;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=45, nullable=true)
     */
    private $barcode;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=20, scale=5, nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=200, nullable=true)
     */
    private $remarks;

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
     * @ORM\Column(name="cbm", type="decimal", precision=20, scale=5, nullable=true)
     */
    private $cbm;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_code", type="string", length=45, nullable=true)
     */
    private $variantCode;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_name", type="string", length=200, nullable=true)
     */
    private $variantName;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_alias", type="string", length=45, nullable=true)
     */
    private $variantAlias;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="full_combined_name", type="string", length=200, nullable=true)
     */
    private $fullCombinedName;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name", type="string", length=100, nullable=true)
     */
    private $itemName;

    /**
     * @var string
     *
     * @ORM\Column(name="variant_sku", type="string", length=45, nullable=true)
     */
    private $variantSku;

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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtInventoryItemVariant
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
     * Set combinedName
     *
     * @param string $combinedName
     *
     * @return NmtInventoryItemVariant
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
     * Set price
     *
     * @param string $price
     *
     * @return NmtInventoryItemVariant
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemVariant
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
     * Set upc
     *
     * @param string $upc
     *
     * @return NmtInventoryItemVariant
     */
    public function setUpc($upc)
    {
        $this->upc = $upc;

        return $this;
    }

    /**
     * Get upc
     *
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * Set ean13
     *
     * @param string $ean13
     *
     * @return NmtInventoryItemVariant
     */
    public function setEan13($ean13)
    {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * Get ean13
     *
     * @return string
     */
    public function getEan13()
    {
        return $this->ean13;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     *
     * @return NmtInventoryItemVariant
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set weight
     *
     * @param string $weight
     *
     * @return NmtInventoryItemVariant
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemVariant
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
     * Set version
     *
     * @param integer $version
     *
     * @return NmtInventoryItemVariant
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
     * @return NmtInventoryItemVariant
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
     * Set cbm
     *
     * @param string $cbm
     *
     * @return NmtInventoryItemVariant
     */
    public function setCbm($cbm)
    {
        $this->cbm = $cbm;

        return $this;
    }

    /**
     * Get cbm
     *
     * @return string
     */
    public function getCbm()
    {
        return $this->cbm;
    }

    /**
     * Set variantCode
     *
     * @param string $variantCode
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantCode($variantCode)
    {
        $this->variantCode = $variantCode;

        return $this;
    }

    /**
     * Get variantCode
     *
     * @return string
     */
    public function getVariantCode()
    {
        return $this->variantCode;
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
     * Set variantAlias
     *
     * @param string $variantAlias
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantAlias($variantAlias)
    {
        $this->variantAlias = $variantAlias;

        return $this;
    }

    /**
     * Get variantAlias
     *
     * @return string
     */
    public function getVariantAlias()
    {
        return $this->variantAlias;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryItemVariant
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;

        return $this;
    }

    /**
     * Get sysNumber
     *
     * @return string
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * Set fullCombinedName
     *
     * @param string $fullCombinedName
     *
     * @return NmtInventoryItemVariant
     */
    public function setFullCombinedName($fullCombinedName)
    {
        $this->fullCombinedName = $fullCombinedName;

        return $this;
    }

    /**
     * Get fullCombinedName
     *
     * @return string
     */
    public function getFullCombinedName()
    {
        return $this->fullCombinedName;
    }

    /**
     * Set itemName
     *
     * @param string $itemName
     *
     * @return NmtInventoryItemVariant
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get itemName
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set variantSku
     *
     * @param string $variantSku
     *
     * @return NmtInventoryItemVariant
     */
    public function setVariantSku($variantSku)
    {
        $this->variantSku = $variantSku;

        return $this;
    }

    /**
     * Get variantSku
     *
     * @return string
     */
    public function getVariantSku()
    {
        return $this->variantSku;
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
