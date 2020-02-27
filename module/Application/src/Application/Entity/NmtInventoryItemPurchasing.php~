<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemPurchasing
 *
 * @ORM\Table(name="nmt_inventory_item_purchasing", indexes={@ORM\Index(name="nmt_inventory_item_purchasing_FK3_idx", columns={"currency_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK4_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK2_idx", columns={"vendor_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK5_idx", columns={"pmt_term_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK6_idx", columns={"pmt_method_id"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK7_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_item_purchasing_FK8_idx", columns={"item_id"})})
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
     * @ORM\Column(name="token", type="string", length=50, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=50, nullable=true)
     */
    private $checksum;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_preferred_vendor", type="boolean", nullable=true)
     */
    private $isPreferredVendor;

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
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $conversionFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_text", type="string", length=45, nullable=true)
     */
    private $conversionText;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_unit_price", type="decimal", precision=15, scale=4, nullable=false)
     */
    private $vendorUnitPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_valid_from", type="datetime", nullable=true)
     */
    private $priceValidFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="price_valid_to", type="datetime", nullable=true)
     */
    private $priceValidTo;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_term_id", type="string", length=45, nullable=true)
     */
    private $deliveryTermId;

    /**
     * @var string
     *
     * @ORM\Column(name="lead_time", type="string", length=50, nullable=true)
     */
    private $leadTime;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=45, nullable=true)
     */
    private $remarks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * @var \Application\Entity\NmtBpVendor
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtBpVendor")
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
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
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
     * @var \Application\Entity\NmtApplicationPmtTerm
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtTerm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pmt_term_id", referencedColumnName="id")
     * })
     */
    private $pmtTerm;

    /**
     * @var \Application\Entity\NmtApplicationPmtMethod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pmt_method_id", referencedColumnName="id")
     * })
     */
    private $pmtMethod;

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
     * @return NmtInventoryItemPurchasing
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
     * @return NmtInventoryItemPurchasing
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
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;

        return $this;
    }

    /**
     * Get conversionFactor
     *
     * @return string
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * Set conversionText
     *
     * @param string $conversionText
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;

        return $this;
    }

    /**
     * Get conversionText
     *
     * @return string
     */
    public function getConversionText()
    {
        return $this->conversionText;
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
     * Set priceValidTo
     *
     * @param \DateTime $priceValidTo
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setPriceValidTo($priceValidTo)
    {
        $this->priceValidTo = $priceValidTo;

        return $this;
    }

    /**
     * Get priceValidTo
     *
     * @return \DateTime
     */
    public function getPriceValidTo()
    {
        return $this->priceValidTo;
    }

    /**
     * Set deliveryTermId
     *
     * @param string $deliveryTermId
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setDeliveryTermId($deliveryTermId)
    {
        $this->deliveryTermId = $deliveryTermId;

        return $this;
    }

    /**
     * Get deliveryTermId
     *
     * @return string
     */
    public function getDeliveryTermId()
    {
        return $this->deliveryTermId;
    }

    /**
     * Set leadTime
     *
     * @param string $leadTime
     *
     * @return NmtInventoryItemPurchasing
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemPurchasing
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemPurchasing
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryItemPurchasing
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
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setVendor(\Application\Entity\NmtBpVendor $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\NmtBpVendor
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

    /**
     * Set pmtTerm
     *
     * @param \Application\Entity\NmtApplicationPmtTerm $pmtTerm
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setPmtTerm(\Application\Entity\NmtApplicationPmtTerm $pmtTerm = null)
    {
        $this->pmtTerm = $pmtTerm;

        return $this;
    }

    /**
     * Get pmtTerm
     *
     * @return \Application\Entity\NmtApplicationPmtTerm
     */
    public function getPmtTerm()
    {
        return $this->pmtTerm;
    }

    /**
     * Set pmtMethod
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $pmtMethod
     *
     * @return NmtInventoryItemPurchasing
     */
    public function setPmtMethod(\Application\Entity\NmtApplicationPmtMethod $pmtMethod = null)
    {
        $this->pmtMethod = $pmtMethod;

        return $this;
    }

    /**
     * Get pmtMethod
     *
     * @return \Application\Entity\NmtApplicationPmtMethod
     */
    public function getPmtMethod()
    {
        return $this->pmtMethod;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryItemPurchasing
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
}
