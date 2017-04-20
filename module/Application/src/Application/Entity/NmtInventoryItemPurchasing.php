<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemPurchasing
 *
 * @ORM\Table(name="nmt_inventory_item_purchasing", indexes={@ORM\Index(name="nmt_inventory_item_purchasing_FK1_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK2_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK3_idx", columns={"currency"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK4_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtInventoryItemPurchasing
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
     * @ORM\Column(name="vendor_item_code", type="string", length=45, nullable=false)
     */
    private $vendorItemCode;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_unit", type="string", length=45, nullable=false)
     */
    private $vendorItemUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="convertion_factor", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $convertionFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_unit_price", type="decimal", precision=15, scale=4, nullable=false)
     */
    private $vendorUnitPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_valid_from", type="datetime", nullable=false)
     */
    private $priceValidFrom = 'CURRENT_TIMESTAMP';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_preferred_vendor", type="boolean", nullable=true)
     */
    private $isPreferredVendor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     * @var \Application\Entity\NmtVendor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtVendor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency", referencedColumnName="id")
     * })
     */
    private $currency;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set vendorItemCode
     *
     * @param string $vendorItemCode
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;

        return $this;
    }

    /**
     * Get vendorItemCode
     *
     * @return string
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     * Set vendorItemUnit
     *
     * @param string $vendorItemUnit
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setVendorItemUnit($vendorItemUnit)
    {
        $this->vendorItemUnit = $vendorItemUnit;

        return $this;
    }

    /**
     * Get vendorItemUnit
     *
     * @return string
     */
    public function getVendorItemUnit()
    {
        return $this->vendorItemUnit;
    }

    /**
     * Set convertionFactor
     *
     * @param string $convertionFactor
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setConvertionFactor($convertionFactor)
    {
        $this->convertionFactor = $convertionFactor;

        return $this;
    }

    /**
     * Get convertionFactor
     *
     * @return string
     */
    public function getConvertionFactor()
    {
        return $this->convertionFactor;
    }

    /**
     * Set vendorUnitPrice
     *
     * @param string $vendorUnitPrice
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setVendorUnitPrice($vendorUnitPrice)
    {
        $this->vendorUnitPrice = $vendorUnitPrice;

        return $this;
    }

    /**
     * Get vendorUnitPrice
     *
     * @return string
     */
    public function getVendorUnitPrice()
    {
        return $this->vendorUnitPrice;
    }

    /**
     * Set priceValidFrom
     *
     * @param \DateTime $priceValidFrom
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setPriceValidFrom($priceValidFrom)
    {
        $this->priceValidFrom = $priceValidFrom;

        return $this;
    }

    /**
     * Get priceValidFrom
     *
     * @return \DateTime
     */
    public function getPriceValidFrom()
    {
        return $this->priceValidFrom;
    }

    /**
     * Set isPreferredVendor
     *
     * @param boolean $isPreferredVendor
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setIsPreferredVendor($isPreferredVendor)
    {
        $this->isPreferredVendor = $isPreferredVendor;

        return $this;
    }

    /**
     * Get isPreferredVendor
     *
     * @return boolean
     */
    public function getIsPreferredVendor()
    {
        return $this->isPreferredVendor;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItemPurchasing
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemPurchasing
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
     * Set vendor
     *
     * @param \Application\Entity\NmtVendor $vendor
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setVendor(\Application\Entity\NmtVendor $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\NmtVendor
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setCurrency(\Application\Entity\NmtApplicationCurrency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemPurchasing
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
}
