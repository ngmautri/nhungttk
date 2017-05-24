<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryTrx
 *
 * @ORM\Table(name="nmt_inventory_trx", indexes={@ORM\Index(name="nmt_inventory_trx_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_trx_FK1_idx1", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_trx_FK3_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_trx_FK4_idx", columns={"pr_row_id"}), @ORM\Index(name="nmt_inventory_trx_FK5_idx", columns={"currency_id"}), @ORM\Index(name="nmt_inventory_trx_FK7_idx", columns={"pmt_method_id"}), @ORM\Index(name="nmt_inventory_trx_FK5_idx1", columns={"vendor_id"}), @ORM\Index(name="nmt_inventory_trx_FK8_idx", columns={"issue_for"})})
 * @ORM\Entity
 */
class NmtInventoryTrx
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
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trx_date", type="datetime", nullable=false)
     */
    private $trxDate = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="trx_type_id", type="integer", nullable=true)
     */
    private $trxTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="flow", type="string", nullable=false)
     */
    private $flow;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_change_by", type="integer", nullable=true)
     */
    private $lastChangeBy;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_preferred_vendor", type="boolean", nullable=true)
     */
    private $isPreferredVendor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_unit", type="string", length=45, nullable=true)
     */
    private $vendorItemUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_code", type="string", length=45, nullable=true)
     */
    private $vendorItemCode;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=true)
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
     * @ORM\Column(name="vendor_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $vendorUnitPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="pmt_term_id", type="integer", nullable=true)
     */
    private $pmtTermId;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_term_id", type="string", length=45, nullable=true)
     */
    private $deliveryTermId;

    /**
     * @var string
     *
     * @ORM\Column(name="lead_time", type="string", length=45, nullable=true)
     */
    private $leadTime;

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
     *   @ORM\JoinColumn(name="wh_id", referencedColumnName="id")
     * })
     */
    private $wh;

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
     * @var \Application\Entity\NmtProcurePrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_row_id", referencedColumnName="id")
     * })
     */
    private $prRow;

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
     * @var \Application\Entity\NmtApplicationPmtMethod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationPmtMethod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pmt_method_id", referencedColumnName="id")
     * })
     */
    private $pmtMethod;

    /**
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_for", referencedColumnName="id")
     * })
     */
    private $issueFor;



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
     * @return NmtInventoryTrx
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
     * @return NmtInventoryTrx
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
     * Set trxDate
     *
     * @param \DateTime $trxDate
     *
     * @return NmtInventoryTrx
     */
    public function setTrxDate($trxDate)
    {
        $this->trxDate = $trxDate;

        return $this;
    }

    /**
     * Get trxDate
     *
     * @return \DateTime
     */
    public function getTrxDate()
    {
        return $this->trxDate;
    }

    /**
     * Set trxTypeId
     *
     * @param integer $trxTypeId
     *
     * @return NmtInventoryTrx
     */
    public function setTrxTypeId($trxTypeId)
    {
        $this->trxTypeId = $trxTypeId;

        return $this;
    }

    /**
     * Get trxTypeId
     *
     * @return integer
     */
    public function getTrxTypeId()
    {
        return $this->trxTypeId;
    }

    /**
     * Set flow
     *
     * @param string $flow
     *
     * @return NmtInventoryTrx
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;

        return $this;
    }

    /**
     * Get flow
     *
     * @return string
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return NmtInventoryTrx
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryTrx
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryTrx
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
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return NmtInventoryTrx
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtInventoryTrx
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryTrx
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryTrx
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
     * Set lastChangeBy
     *
     * @param integer $lastChangeBy
     *
     * @return NmtInventoryTrx
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return integer
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set isPreferredVendor
     *
     * @param boolean $isPreferredVendor
     *
     * @return NmtInventoryTrx
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
     * Set vendorItemUnit
     *
     * @param string $vendorItemUnit
     *
     * @return NmtInventoryTrx
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
     * Set vendorItemCode
     *
     * @param string $vendorItemCode
     *
     * @return NmtInventoryTrx
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
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtInventoryTrx
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
     * @return NmtInventoryTrx
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
     * @return NmtInventoryTrx
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
     * Set pmtTermId
     *
     * @param integer $pmtTermId
     *
     * @return NmtInventoryTrx
     */
    public function setPmtTermId($pmtTermId)
    {
        $this->pmtTermId = $pmtTermId;

        return $this;
    }

    /**
     * Get pmtTermId
     *
     * @return integer
     */
    public function getPmtTermId()
    {
        return $this->pmtTermId;
    }

    /**
     * Set deliveryTermId
     *
     * @param string $deliveryTermId
     *
     * @return NmtInventoryTrx
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
     * @return NmtInventoryTrx
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryTrx
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
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return NmtInventoryTrx
     */
    public function setWh(\Application\Entity\NmtInventoryWarehouse $wh = null)
    {
        $this->wh = $wh;

        return $this;
    }

    /**
     * Get wh
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWh()
    {
        return $this->wh;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryTrx
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
     * Set prRow
     *
     * @param \Application\Entity\NmtProcurePrRow $prRow
     *
     * @return NmtInventoryTrx
     */
    public function setPrRow(\Application\Entity\NmtProcurePrRow $prRow = null)
    {
        $this->prRow = $prRow;

        return $this;
    }

    /**
     * Get prRow
     *
     * @return \Application\Entity\NmtProcurePrRow
     */
    public function getPrRow()
    {
        return $this->prRow;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\NmtBpVendor $vendor
     *
     * @return NmtInventoryTrx
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
     * @return NmtInventoryTrx
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
     * Set pmtMethod
     *
     * @param \Application\Entity\NmtApplicationPmtMethod $pmtMethod
     *
     * @return NmtInventoryTrx
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
     * Set issueFor
     *
     * @param \Application\Entity\NmtInventoryItem $issueFor
     *
     * @return NmtInventoryTrx
     */
    public function setIssueFor(\Application\Entity\NmtInventoryItem $issueFor = null)
    {
        $this->issueFor = $issueFor;

        return $this;
    }

    /**
     * Get issueFor
     *
     * @return \Application\Entity\NmtInventoryItem
     */
    public function getIssueFor()
    {
        return $this->issueFor;
    }
}
