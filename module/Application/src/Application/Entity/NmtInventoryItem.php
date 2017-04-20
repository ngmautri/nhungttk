<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItem
 *
 * @ORM\Table(name="nmt_inventory_item", indexes={@ORM\Index(name="nmt_inventory_item_FK2_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_FK2_idx1", columns={"warehouse_id"}), @ORM\Index(name="nmt_inventory_item_FK4_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtInventoryItem
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
     * @ORM\Column(name="item_sku", type="string", length=45, nullable=false)
     */
    private $itemSku;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name", type="string", length=100, nullable=false)
     */
    private $itemName;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name_foreign", type="string", length=100, nullable=true)
     */
    private $itemNameForeign;

    /**
     * @var string
     *
     * @ORM\Column(name="item_description", type="string", length=255, nullable=true)
     */
    private $itemDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="item_type", type="string", length=1, nullable=true)
     */
    private $itemType;

    /**
     * @var string
     *
     * @ORM\Column(name="item_group", type="string", length=45, nullable=true)
     */
    private $itemGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="item_category", type="string", length=45, nullable=true)
     */
    private $itemCategory;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_stocked", type="boolean", nullable=true)
     */
    private $isStocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sale_item", type="boolean", nullable=true)
     */
    private $isSaleItem;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_purchased", type="boolean", nullable=true)
     */
    private $isPurchased;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_fixed_asset", type="boolean", nullable=true)
     */
    private $isFixedAsset;

    /**
     * @var string
     *
     * @ORM\Column(name="uom", type="string", length=45, nullable=true)
     */
    private $uom;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=45, nullable=true)
     */
    private $barcode;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_code", type="string", length=100, nullable=true)
     */
    private $manufacturerCode;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_catalog", type="string", length=45, nullable=true)
     */
    private $manufacturerCatalog;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_model", type="string", length=45, nullable=true)
     */
    private $manufacturerModel;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturer_serial", type="string", length=45, nullable=true)
     */
    private $manufacturerSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number", type="string", length=45, nullable=true)
     */
    private $serialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="last_purchase_price", type="decimal", precision=19, scale=6, nullable=true)
     */
    private $lastPurchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="last_purchase_currency", type="string", length=3, nullable=true)
     */
    private $lastPurchaseCurrency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_purchase_date", type="datetime", nullable=true)
     */
    private $lastPurchaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="lead_time", type="string", length=50, nullable=true)
     */
    private $leadTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from_date", type="datetime", nullable=true)
     */
    private $validFromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to_date", type="datetime", nullable=true)
     */
    private $validToDate;

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
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     * })
     */
    private $warehouse;

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
     * Set itemSku
     *
     * @param string $itemSku
     *
     * @return NmtInventoryItem
     */
    public function setItemSku($itemSku)
    {
        $this->itemSku = $itemSku;

        return $this;
    }

    /**
     * Get itemSku
     *
     * @return string
     */
    public function getItemSku()
    {
        return $this->itemSku;
    }

    /**
     * Set itemName
     *
     * @param string $itemName
     *
     * @return NmtInventoryItem
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
     * Set itemNameForeign
     *
     * @param string $itemNameForeign
     *
     * @return NmtInventoryItem
     */
    public function setItemNameForeign($itemNameForeign)
    {
        $this->itemNameForeign = $itemNameForeign;

        return $this;
    }

    /**
     * Get itemNameForeign
     *
     * @return string
     */
    public function getItemNameForeign()
    {
        return $this->itemNameForeign;
    }

    /**
     * Set itemDescription
     *
     * @param string $itemDescription
     *
     * @return NmtInventoryItem
     */
    public function setItemDescription($itemDescription)
    {
        $this->itemDescription = $itemDescription;

        return $this;
    }

    /**
     * Get itemDescription
     *
     * @return string
     */
    public function getItemDescription()
    {
        return $this->itemDescription;
    }

    /**
     * Set itemType
     *
     * @param string $itemType
     *
     * @return NmtInventoryItem
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set itemGroup
     *
     * @param string $itemGroup
     *
     * @return NmtInventoryItem
     */
    public function setItemGroup($itemGroup)
    {
        $this->itemGroup = $itemGroup;

        return $this;
    }

    /**
     * Get itemGroup
     *
     * @return string
     */
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     * Set itemCategory
     *
     * @param string $itemCategory
     *
     * @return NmtInventoryItem
     */
    public function setItemCategory($itemCategory)
    {
        $this->itemCategory = $itemCategory;

        return $this;
    }

    /**
     * Get itemCategory
     *
     * @return string
     */
    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItem
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
     * Set isStocked
     *
     * @param boolean $isStocked
     *
     * @return NmtInventoryItem
     */
    public function setIsStocked($isStocked)
    {
        $this->isStocked = $isStocked;

        return $this;
    }

    /**
     * Get isStocked
     *
     * @return boolean
     */
    public function getIsStocked()
    {
        return $this->isStocked;
    }

    /**
     * Set isSaleItem
     *
     * @param boolean $isSaleItem
     *
     * @return NmtInventoryItem
     */
    public function setIsSaleItem($isSaleItem)
    {
        $this->isSaleItem = $isSaleItem;

        return $this;
    }

    /**
     * Get isSaleItem
     *
     * @return boolean
     */
    public function getIsSaleItem()
    {
        return $this->isSaleItem;
    }

    /**
     * Set isPurchased
     *
     * @param boolean $isPurchased
     *
     * @return NmtInventoryItem
     */
    public function setIsPurchased($isPurchased)
    {
        $this->isPurchased = $isPurchased;

        return $this;
    }

    /**
     * Get isPurchased
     *
     * @return boolean
     */
    public function getIsPurchased()
    {
        return $this->isPurchased;
    }

    /**
     * Set isFixedAsset
     *
     * @param boolean $isFixedAsset
     *
     * @return NmtInventoryItem
     */
    public function setIsFixedAsset($isFixedAsset)
    {
        $this->isFixedAsset = $isFixedAsset;

        return $this;
    }

    /**
     * Get isFixedAsset
     *
     * @return boolean
     */
    public function getIsFixedAsset()
    {
        return $this->isFixedAsset;
    }

    /**
     * Set uom
     *
     * @param string $uom
     *
     * @return NmtInventoryItem
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
     * Set barcode
     *
     * @param string $barcode
     *
     * @return NmtInventoryItem
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
     * Set status
     *
     * @param integer $status
     *
     * @return NmtInventoryItem
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItem
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
     * Set manufacturerCode
     *
     * @param string $manufacturerCode
     *
     * @return NmtInventoryItem
     */
    public function setManufacturerCode($manufacturerCode)
    {
        $this->manufacturerCode = $manufacturerCode;

        return $this;
    }

    /**
     * Get manufacturerCode
     *
     * @return string
     */
    public function getManufacturerCode()
    {
        return $this->manufacturerCode;
    }

    /**
     * Set manufacturerCatalog
     *
     * @param string $manufacturerCatalog
     *
     * @return NmtInventoryItem
     */
    public function setManufacturerCatalog($manufacturerCatalog)
    {
        $this->manufacturerCatalog = $manufacturerCatalog;

        return $this;
    }

    /**
     * Get manufacturerCatalog
     *
     * @return string
     */
    public function getManufacturerCatalog()
    {
        return $this->manufacturerCatalog;
    }

    /**
     * Set manufacturerModel
     *
     * @param string $manufacturerModel
     *
     * @return NmtInventoryItem
     */
    public function setManufacturerModel($manufacturerModel)
    {
        $this->manufacturerModel = $manufacturerModel;

        return $this;
    }

    /**
     * Get manufacturerModel
     *
     * @return string
     */
    public function getManufacturerModel()
    {
        return $this->manufacturerModel;
    }

    /**
     * Set manufacturerSerial
     *
     * @param string $manufacturerSerial
     *
     * @return NmtInventoryItem
     */
    public function setManufacturerSerial($manufacturerSerial)
    {
        $this->manufacturerSerial = $manufacturerSerial;

        return $this;
    }

    /**
     * Get manufacturerSerial
     *
     * @return string
     */
    public function getManufacturerSerial()
    {
        return $this->manufacturerSerial;
    }

    /**
     * Set serialNumber
     *
     * @param string $serialNumber
     *
     * @return NmtInventoryItem
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set lastPurchasePrice
     *
     * @param string $lastPurchasePrice
     *
     * @return NmtInventoryItem
     */
    public function setLastPurchasePrice($lastPurchasePrice)
    {
        $this->lastPurchasePrice = $lastPurchasePrice;

        return $this;
    }

    /**
     * Get lastPurchasePrice
     *
     * @return string
     */
    public function getLastPurchasePrice()
    {
        return $this->lastPurchasePrice;
    }

    /**
     * Set lastPurchaseCurrency
     *
     * @param string $lastPurchaseCurrency
     *
     * @return NmtInventoryItem
     */
    public function setLastPurchaseCurrency($lastPurchaseCurrency)
    {
        $this->lastPurchaseCurrency = $lastPurchaseCurrency;

        return $this;
    }

    /**
     * Get lastPurchaseCurrency
     *
     * @return string
     */
    public function getLastPurchaseCurrency()
    {
        return $this->lastPurchaseCurrency;
    }

    /**
     * Set lastPurchaseDate
     *
     * @param \DateTime $lastPurchaseDate
     *
     * @return NmtInventoryItem
     */
    public function setLastPurchaseDate($lastPurchaseDate)
    {
        $this->lastPurchaseDate = $lastPurchaseDate;

        return $this;
    }

    /**
     * Get lastPurchaseDate
     *
     * @return \DateTime
     */
    public function getLastPurchaseDate()
    {
        return $this->lastPurchaseDate;
    }

    /**
     * Set leadTime
     *
     * @param string $leadTime
     *
     * @return NmtInventoryItem
     */
    public function setLeadTime($leadTime)
    {
        $this->leadTime = $leadTime;

        return $this;
    }

    /**
     * Get leadTime
     *
     * @return string
     */
    public function getLeadTime()
    {
        return $this->leadTime;
    }

    /**
     * Set validFromDate
     *
     * @param \DateTime $validFromDate
     *
     * @return NmtInventoryItem
     */
    public function setValidFromDate($validFromDate)
    {
        $this->validFromDate = $validFromDate;

        return $this;
    }

    /**
     * Get validFromDate
     *
     * @return \DateTime
     */
    public function getValidFromDate()
    {
        return $this->validFromDate;
    }

    /**
     * Set validToDate
     *
     * @param \DateTime $validToDate
     *
     * @return NmtInventoryItem
     */
    public function setValidToDate($validToDate)
    {
        $this->validToDate = $validToDate;

        return $this;
    }

    /**
     * Get validToDate
     *
     * @return \DateTime
     */
    public function getValidToDate()
    {
        return $this->validToDate;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItem
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
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtInventoryItem
     */
    public function setWarehouse(\Application\Entity\NmtInventoryWarehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtInventoryItem
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
