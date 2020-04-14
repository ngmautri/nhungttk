<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItem
 *
 * @ORM\Table(name="nmt_inventory_item", indexes={@ORM\Index(name="nmt_inventory_item_IDX1", columns={"is_active"}), @ORM\Index(name="nmt_inventory_item_IDX2", columns={"is_fixed_asset"}), @ORM\Index(name="nmt_inventory_item_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_FK2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_item_FK4_idx", columns={"company_id"}), @ORM\Index(name="nmt_inventory_item_FK5_idx", columns={"last_pr_row"}), @ORM\Index(name="nmt_inventory_item_FK6_idx", columns={"last_po_row"}), @ORM\Index(name="nmt_inventory_item_FK7_idx", columns={"last_ap_invoice_row"}), @ORM\Index(name="nmt_inventory_item_FK8_idx", columns={"last_trx_row"}), @ORM\Index(name="nmt_inventory_item_FK9_idx", columns={"last_purchasing"}), @ORM\Index(name="nmt_inventory_item_FK10_idx", columns={"item_group_id"}), @ORM\Index(name="nmt_inventory_item_FK3_idx", columns={"standard_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK11_idx", columns={"stock_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK12_idx", columns={"cogs_account_id"}), @ORM\Index(name="nmt_inventory_item_FK13_idx", columns={"purchase_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK14_idx", columns={"sales_uom_id"}), @ORM\Index(name="nmt_inventory_item_FK15_idx", columns={"inventory_account_id"}), @ORM\Index(name="nmt_inventory_item_FK16_idx", columns={"expense_account_id"}), @ORM\Index(name="nmt_inventory_item_FK17_idx", columns={"revenue_account_id"}), @ORM\Index(name="nmt_inventory_item_FK18_idx", columns={"default_warehouse_id"})})
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
     * @var integer
     *
     * @ORM\Column(name="warehouse_id", type="integer", nullable=true)
     */
    private $warehouseId;

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
     * @ORM\Column(name="item_type", type="string", nullable=true)
     */
    private $itemType;

    /**
     * @var string
     *
     * @ORM\Column(name="item_category", type="string", length=45, nullable=true)
     */
    private $itemCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=100, nullable=true)
     */
    private $keywords;

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
     * @var boolean
     *
     * @ORM\Column(name="is_sparepart", type="boolean", nullable=true)
     */
    private $isSparepart;

    /**
     * @var string
     *
     * @ORM\Column(name="uom", type="string", length=45, nullable=true)
     */
    private $uom;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=40, nullable=true)
     */
    private $barcode;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode39", type="string", length=40, nullable=true)
     */
    private $barcode39;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode128", type="string", length=50, nullable=true)
     */
    private $barcode128;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="manufacturer", type="string", length=50, nullable=true)
     */
    private $manufacturer;

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
     * @ORM\Column(name="origin", type="string", length=45, nullable=true)
     */
    private $origin;

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
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="item_internal_label", type="string", length=50, nullable=true)
     */
    private $itemInternalLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="asset_label", type="string", length=45, nullable=true)
     */
    private $assetLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="sparepart_label", type="string", length=45, nullable=true)
     */
    private $sparepartLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="local_availabiliy", type="boolean", nullable=true)
     */
    private $localAvailabiliy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_number", type="string", length=45, nullable=true)
     */
    private $docNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="monitored_by", type="string", nullable=true)
     */
    private $monitoredBy;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks_text", type="text", length=65535, nullable=true)
     */
    private $remarksText;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="item_sku1", type="string", length=45, nullable=true)
     */
    private $itemSku1;

    /**
     * @var string
     *
     * @ORM\Column(name="item_sku2", type="string", length=45, nullable=true)
     */
    private $itemSku2;

    /**
     * @var integer
     *
     * @ORM\Column(name="asset_group", type="integer", nullable=true)
     */
    private $assetGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="asset_class", type="integer", nullable=true)
     */
    private $assetClass;

    /**
     * @var string
     *
     * @ORM\Column(name="stock_uom_convert_factor", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $stockUomConvertFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_uom_convert_factor", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $purchaseUomConvertFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="sales_uom_convert_factor", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $salesUomConvertFactor;

    /**
     * @var integer
     *
     * @ORM\Column(name="capacity", type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @var string
     *
     * @ORM\Column(name="avg_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $avgUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="standard_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $standardPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_type_id", type="integer", nullable=true)
     */
    private $itemTypeId;

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
     * @var \Application\Entity\NmtInventoryItemGroup
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_group_id", referencedColumnName="id")
     * })
     */
    private $itemGroup;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stock_uom_id", referencedColumnName="id")
     * })
     */
    private $stockUom;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cogs_account_id", referencedColumnName="id")
     * })
     */
    private $cogsAccount;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="purchase_uom_id", referencedColumnName="id")
     * })
     */
    private $purchaseUom;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sales_uom_id", referencedColumnName="id")
     * })
     */
    private $salesUom;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_account_id", referencedColumnName="id")
     * })
     */
    private $inventoryAccount;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="expense_account_id", referencedColumnName="id")
     * })
     */
    private $expenseAccount;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revenue_account_id", referencedColumnName="id")
     * })
     */
    private $revenueAccount;

    /**
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="default_warehouse_id", referencedColumnName="id")
     * })
     */
    private $defaultWarehouse;

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
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="standard_uom_id", referencedColumnName="id")
     * })
     */
    private $standardUom;

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
     * @var \Application\Entity\NmtProcurePrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_pr_row", referencedColumnName="id")
     * })
     */
    private $lastPrRow;

    /**
     * @var \Application\Entity\NmtProcurePoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_po_row", referencedColumnName="id")
     * })
     */
    private $lastPoRow;

    /**
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_ap_invoice_row", referencedColumnName="id")
     * })
     */
    private $lastApInvoiceRow;

    /**
     * @var \Application\Entity\NmtInventoryTrx
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryTrx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_trx_row", referencedColumnName="id")
     * })
     */
    private $lastTrxRow;

    /**
     * @var \Application\Entity\NmtInventoryItemPurchasing
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemPurchasing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_purchasing", referencedColumnName="id")
     * })
     */
    private $lastPurchasing;



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
     * Set warehouseId
     *
     * @param integer $warehouseId
     *
     * @return NmtInventoryItem
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    /**
     * Get warehouseId
     *
     * @return integer
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
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
     * Set keywords
     *
     * @param string $keywords
     *
     * @return NmtInventoryItem
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
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
     * Set isSparepart
     *
     * @param boolean $isSparepart
     *
     * @return NmtInventoryItem
     */
    public function setIsSparepart($isSparepart)
    {
        $this->isSparepart = $isSparepart;

        return $this;
    }

    /**
     * Get isSparepart
     *
     * @return boolean
     */
    public function getIsSparepart()
    {
        return $this->isSparepart;
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
     * Set barcode39
     *
     * @param string $barcode39
     *
     * @return NmtInventoryItem
     */
    public function setBarcode39($barcode39)
    {
        $this->barcode39 = $barcode39;

        return $this;
    }

    /**
     * Get barcode39
     *
     * @return string
     */
    public function getBarcode39()
    {
        return $this->barcode39;
    }

    /**
     * Set barcode128
     *
     * @param string $barcode128
     *
     * @return NmtInventoryItem
     */
    public function setBarcode128($barcode128)
    {
        $this->barcode128 = $barcode128;

        return $this;
    }

    /**
     * Get barcode128
     *
     * @return string
     */
    public function getBarcode128()
    {
        return $this->barcode128;
    }

    /**
     * Set status
     *
     * @param string $status
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
     * @return string
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
     * Set manufacturer
     *
     * @param string $manufacturer
     *
     * @return NmtInventoryItem
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
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
     * Set origin
     *
     * @param string $origin
     *
     * @return NmtInventoryItem
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
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
     * Set location
     *
     * @param string $location
     *
     * @return NmtInventoryItem
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set itemInternalLabel
     *
     * @param string $itemInternalLabel
     *
     * @return NmtInventoryItem
     */
    public function setItemInternalLabel($itemInternalLabel)
    {
        $this->itemInternalLabel = $itemInternalLabel;

        return $this;
    }

    /**
     * Get itemInternalLabel
     *
     * @return string
     */
    public function getItemInternalLabel()
    {
        return $this->itemInternalLabel;
    }

    /**
     * Set assetLabel
     *
     * @param string $assetLabel
     *
     * @return NmtInventoryItem
     */
    public function setAssetLabel($assetLabel)
    {
        $this->assetLabel = $assetLabel;

        return $this;
    }

    /**
     * Get assetLabel
     *
     * @return string
     */
    public function getAssetLabel()
    {
        return $this->assetLabel;
    }

    /**
     * Set sparepartLabel
     *
     * @param string $sparepartLabel
     *
     * @return NmtInventoryItem
     */
    public function setSparepartLabel($sparepartLabel)
    {
        $this->sparepartLabel = $sparepartLabel;

        return $this;
    }

    /**
     * Get sparepartLabel
     *
     * @return string
     */
    public function getSparepartLabel()
    {
        return $this->sparepartLabel;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItem
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
     * Set localAvailabiliy
     *
     * @param boolean $localAvailabiliy
     *
     * @return NmtInventoryItem
     */
    public function setLocalAvailabiliy($localAvailabiliy)
    {
        $this->localAvailabiliy = $localAvailabiliy;

        return $this;
    }

    /**
     * Get localAvailabiliy
     *
     * @return boolean
     */
    public function getLocalAvailabiliy()
    {
        return $this->localAvailabiliy;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryItem
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryItem
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
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtInventoryItem
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtInventoryItem
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set docNumber
     *
     * @param string $docNumber
     *
     * @return NmtInventoryItem
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;

        return $this;
    }

    /**
     * Get docNumber
     *
     * @return string
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     * Set monitoredBy
     *
     * @param string $monitoredBy
     *
     * @return NmtInventoryItem
     */
    public function setMonitoredBy($monitoredBy)
    {
        $this->monitoredBy = $monitoredBy;

        return $this;
    }

    /**
     * Get monitoredBy
     *
     * @return string
     */
    public function getMonitoredBy()
    {
        return $this->monitoredBy;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryItem
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
     * Set remarksText
     *
     * @param string $remarksText
     *
     * @return NmtInventoryItem
     */
    public function setRemarksText($remarksText)
    {
        $this->remarksText = $remarksText;

        return $this;
    }

    /**
     * Get remarksText
     *
     * @return string
     */
    public function getRemarksText()
    {
        return $this->remarksText;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryItem
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
     * Set itemSku1
     *
     * @param string $itemSku1
     *
     * @return NmtInventoryItem
     */
    public function setItemSku1($itemSku1)
    {
        $this->itemSku1 = $itemSku1;

        return $this;
    }

    /**
     * Get itemSku1
     *
     * @return string
     */
    public function getItemSku1()
    {
        return $this->itemSku1;
    }

    /**
     * Set itemSku2
     *
     * @param string $itemSku2
     *
     * @return NmtInventoryItem
     */
    public function setItemSku2($itemSku2)
    {
        $this->itemSku2 = $itemSku2;

        return $this;
    }

    /**
     * Get itemSku2
     *
     * @return string
     */
    public function getItemSku2()
    {
        return $this->itemSku2;
    }

    /**
     * Set assetGroup
     *
     * @param integer $assetGroup
     *
     * @return NmtInventoryItem
     */
    public function setAssetGroup($assetGroup)
    {
        $this->assetGroup = $assetGroup;

        return $this;
    }

    /**
     * Get assetGroup
     *
     * @return integer
     */
    public function getAssetGroup()
    {
        return $this->assetGroup;
    }

    /**
     * Set assetClass
     *
     * @param integer $assetClass
     *
     * @return NmtInventoryItem
     */
    public function setAssetClass($assetClass)
    {
        $this->assetClass = $assetClass;

        return $this;
    }

    /**
     * Get assetClass
     *
     * @return integer
     */
    public function getAssetClass()
    {
        return $this->assetClass;
    }

    /**
     * Set stockUomConvertFactor
     *
     * @param string $stockUomConvertFactor
     *
     * @return NmtInventoryItem
     */
    public function setStockUomConvertFactor($stockUomConvertFactor)
    {
        $this->stockUomConvertFactor = $stockUomConvertFactor;

        return $this;
    }

    /**
     * Get stockUomConvertFactor
     *
     * @return string
     */
    public function getStockUomConvertFactor()
    {
        return $this->stockUomConvertFactor;
    }

    /**
     * Set purchaseUomConvertFactor
     *
     * @param string $purchaseUomConvertFactor
     *
     * @return NmtInventoryItem
     */
    public function setPurchaseUomConvertFactor($purchaseUomConvertFactor)
    {
        $this->purchaseUomConvertFactor = $purchaseUomConvertFactor;

        return $this;
    }

    /**
     * Get purchaseUomConvertFactor
     *
     * @return string
     */
    public function getPurchaseUomConvertFactor()
    {
        return $this->purchaseUomConvertFactor;
    }

    /**
     * Set salesUomConvertFactor
     *
     * @param string $salesUomConvertFactor
     *
     * @return NmtInventoryItem
     */
    public function setSalesUomConvertFactor($salesUomConvertFactor)
    {
        $this->salesUomConvertFactor = $salesUomConvertFactor;

        return $this;
    }

    /**
     * Get salesUomConvertFactor
     *
     * @return string
     */
    public function getSalesUomConvertFactor()
    {
        return $this->salesUomConvertFactor;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return NmtInventoryItem
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set avgUnitPrice
     *
     * @param string $avgUnitPrice
     *
     * @return NmtInventoryItem
     */
    public function setAvgUnitPrice($avgUnitPrice)
    {
        $this->avgUnitPrice = $avgUnitPrice;

        return $this;
    }

    /**
     * Get avgUnitPrice
     *
     * @return string
     */
    public function getAvgUnitPrice()
    {
        return $this->avgUnitPrice;
    }

    /**
     * Set standardPrice
     *
     * @param string $standardPrice
     *
     * @return NmtInventoryItem
     */
    public function setStandardPrice($standardPrice)
    {
        $this->standardPrice = $standardPrice;

        return $this;
    }

    /**
     * Get standardPrice
     *
     * @return string
     */
    public function getStandardPrice()
    {
        return $this->standardPrice;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtInventoryItem
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
     * Set itemTypeId
     *
     * @param integer $itemTypeId
     *
     * @return NmtInventoryItem
     */
    public function setItemTypeId($itemTypeId)
    {
        $this->itemTypeId = $itemTypeId;

        return $this;
    }

    /**
     * Get itemTypeId
     *
     * @return integer
     */
    public function getItemTypeId()
    {
        return $this->itemTypeId;
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
     * Set itemGroup
     *
     * @param \Application\Entity\NmtInventoryItemGroup $itemGroup
     *
     * @return NmtInventoryItem
     */
    public function setItemGroup(\Application\Entity\NmtInventoryItemGroup $itemGroup = null)
    {
        $this->itemGroup = $itemGroup;

        return $this;
    }

    /**
     * Get itemGroup
     *
     * @return \Application\Entity\NmtInventoryItemGroup
     */
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     * Set stockUom
     *
     * @param \Application\Entity\NmtApplicationUom $stockUom
     *
     * @return NmtInventoryItem
     */
    public function setStockUom(\Application\Entity\NmtApplicationUom $stockUom = null)
    {
        $this->stockUom = $stockUom;

        return $this;
    }

    /**
     * Get stockUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getStockUom()
    {
        return $this->stockUom;
    }

    /**
     * Set cogsAccount
     *
     * @param \Application\Entity\FinAccount $cogsAccount
     *
     * @return NmtInventoryItem
     */
    public function setCogsAccount(\Application\Entity\FinAccount $cogsAccount = null)
    {
        $this->cogsAccount = $cogsAccount;

        return $this;
    }

    /**
     * Get cogsAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getCogsAccount()
    {
        return $this->cogsAccount;
    }

    /**
     * Set purchaseUom
     *
     * @param \Application\Entity\NmtApplicationUom $purchaseUom
     *
     * @return NmtInventoryItem
     */
    public function setPurchaseUom(\Application\Entity\NmtApplicationUom $purchaseUom = null)
    {
        $this->purchaseUom = $purchaseUom;

        return $this;
    }

    /**
     * Get purchaseUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getPurchaseUom()
    {
        return $this->purchaseUom;
    }

    /**
     * Set salesUom
     *
     * @param \Application\Entity\NmtApplicationUom $salesUom
     *
     * @return NmtInventoryItem
     */
    public function setSalesUom(\Application\Entity\NmtApplicationUom $salesUom = null)
    {
        $this->salesUom = $salesUom;

        return $this;
    }

    /**
     * Get salesUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getSalesUom()
    {
        return $this->salesUom;
    }

    /**
     * Set inventoryAccount
     *
     * @param \Application\Entity\FinAccount $inventoryAccount
     *
     * @return NmtInventoryItem
     */
    public function setInventoryAccount(\Application\Entity\FinAccount $inventoryAccount = null)
    {
        $this->inventoryAccount = $inventoryAccount;

        return $this;
    }

    /**
     * Get inventoryAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getInventoryAccount()
    {
        return $this->inventoryAccount;
    }

    /**
     * Set expenseAccount
     *
     * @param \Application\Entity\FinAccount $expenseAccount
     *
     * @return NmtInventoryItem
     */
    public function setExpenseAccount(\Application\Entity\FinAccount $expenseAccount = null)
    {
        $this->expenseAccount = $expenseAccount;

        return $this;
    }

    /**
     * Get expenseAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getExpenseAccount()
    {
        return $this->expenseAccount;
    }

    /**
     * Set revenueAccount
     *
     * @param \Application\Entity\FinAccount $revenueAccount
     *
     * @return NmtInventoryItem
     */
    public function setRevenueAccount(\Application\Entity\FinAccount $revenueAccount = null)
    {
        $this->revenueAccount = $revenueAccount;

        return $this;
    }

    /**
     * Get revenueAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getRevenueAccount()
    {
        return $this->revenueAccount;
    }

    /**
     * Set defaultWarehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $defaultWarehouse
     *
     * @return NmtInventoryItem
     */
    public function setDefaultWarehouse(\Application\Entity\NmtInventoryWarehouse $defaultWarehouse = null)
    {
        $this->defaultWarehouse = $defaultWarehouse;

        return $this;
    }

    /**
     * Get defaultWarehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryItem
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
     * Set standardUom
     *
     * @param \Application\Entity\NmtApplicationUom $standardUom
     *
     * @return NmtInventoryItem
     */
    public function setStandardUom(\Application\Entity\NmtApplicationUom $standardUom = null)
    {
        $this->standardUom = $standardUom;

        return $this;
    }

    /**
     * Get standardUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getStandardUom()
    {
        return $this->standardUom;
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

    /**
     * Set lastPrRow
     *
     * @param \Application\Entity\NmtProcurePrRow $lastPrRow
     *
     * @return NmtInventoryItem
     */
    public function setLastPrRow(\Application\Entity\NmtProcurePrRow $lastPrRow = null)
    {
        $this->lastPrRow = $lastPrRow;

        return $this;
    }

    /**
     * Get lastPrRow
     *
     * @return \Application\Entity\NmtProcurePrRow
     */
    public function getLastPrRow()
    {
        return $this->lastPrRow;
    }

    /**
     * Set lastPoRow
     *
     * @param \Application\Entity\NmtProcurePoRow $lastPoRow
     *
     * @return NmtInventoryItem
     */
    public function setLastPoRow(\Application\Entity\NmtProcurePoRow $lastPoRow = null)
    {
        $this->lastPoRow = $lastPoRow;

        return $this;
    }

    /**
     * Get lastPoRow
     *
     * @return \Application\Entity\NmtProcurePoRow
     */
    public function getLastPoRow()
    {
        return $this->lastPoRow;
    }

    /**
     * Set lastApInvoiceRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $lastApInvoiceRow
     *
     * @return NmtInventoryItem
     */
    public function setLastApInvoiceRow(\Application\Entity\FinVendorInvoiceRow $lastApInvoiceRow = null)
    {
        $this->lastApInvoiceRow = $lastApInvoiceRow;

        return $this;
    }

    /**
     * Get lastApInvoiceRow
     *
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    public function getLastApInvoiceRow()
    {
        return $this->lastApInvoiceRow;
    }

    /**
     * Set lastTrxRow
     *
     * @param \Application\Entity\NmtInventoryTrx $lastTrxRow
     *
     * @return NmtInventoryItem
     */
    public function setLastTrxRow(\Application\Entity\NmtInventoryTrx $lastTrxRow = null)
    {
        $this->lastTrxRow = $lastTrxRow;

        return $this;
    }

    /**
     * Get lastTrxRow
     *
     * @return \Application\Entity\NmtInventoryTrx
     */
    public function getLastTrxRow()
    {
        return $this->lastTrxRow;
    }

    /**
     * Set lastPurchasing
     *
     * @param \Application\Entity\NmtInventoryItemPurchasing $lastPurchasing
     *
     * @return NmtInventoryItem
     */
    public function setLastPurchasing(\Application\Entity\NmtInventoryItemPurchasing $lastPurchasing = null)
    {
        $this->lastPurchasing = $lastPurchasing;

        return $this;
    }

    /**
     * Get lastPurchasing
     *
     * @return \Application\Entity\NmtInventoryItemPurchasing
     */
    public function getLastPurchasing()
    {
        return $this->lastPurchasing;
    }
}
