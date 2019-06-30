<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryTrx
 *
 * @ORM\Table(name="nmt_inventory_trx", indexes={@ORM\Index(name="nmt_inventory_trx_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_trx_FK1_idx1", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_trx_FK4_idx", columns={"pr_row_id"}), @ORM\Index(name="nmt_inventory_trx_FK5_idx", columns={"currency_id"}), @ORM\Index(name="nmt_inventory_trx_FK7_idx", columns={"pmt_method_id"}), @ORM\Index(name="nmt_inventory_trx_FK5_idx1", columns={"vendor_id"}), @ORM\Index(name="nmt_inventory_trx_FK9_idx", columns={"invoice_row_id"}), @ORM\Index(name="nmt_inventory_trx_FK10_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_trx_FK11_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_trx_IDX1", columns={"is_active"}), @ORM\Index(name="nmt_inventory_trx_FK12_idx", columns={"pr_id"}), @ORM\Index(name="nmt_inventory_trx_FK12_idx1", columns={"po_id"}), @ORM\Index(name="nmt_inventory_trx_FK14_idx", columns={"vendor_invoice_id"}), @ORM\Index(name="nmt_inventory_trx_FK15_idx", columns={"po_row_id"}), @ORM\Index(name="nmt_inventory_trx_FK17_idx", columns={"inventory_gi_id"}), @ORM\Index(name="nmt_inventory_trx_FK17_idx1", columns={"inventory_gr_id"}), @ORM\Index(name="nmt_inventory_trx_FK19_idx", columns={"inventory_transfer_id"}), @ORM\Index(name="nmt_inventory_trx_FK20_idx", columns={"gr_id"}), @ORM\Index(name="nmt_inventory_trx_FK21_idx", columns={"movement_id"}), @ORM\Index(name="nmt_inventory_trx_FK22_idx", columns={"issue_for"}), @ORM\Index(name="nmt_inventory_trx_FK23_idx", columns={"doc_currency_id"}), @ORM\Index(name="nmt_inventory_trx_FK24_idx", columns={"local_currency_id"}), @ORM\Index(name="nmt_inventory_trx_FK25_idx", columns={"project_id"}), @ORM\Index(name="nmt_inventory_trx_FK26_idx", columns={"cost_center_id"}), @ORM\Index(name="nmt_inventory_trx_FK27_idx", columns={"doc_uom"}), @ORM\Index(name="nmt_inventory_trx_FK28_idx", columns={"posting_period_id"}), @ORM\Index(name="nmt_inventory_trx_FK16_idx", columns={"gr_row_id"}), @ORM\Index(name="nmt_inventory_trx_FK29_idx", columns={"wh_location"})})
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
     * @ORM\Column(name="trx_date", type="datetime", nullable=true)
     */
    private $trxDate;

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
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=4, nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="tax_rate", type="integer", nullable=true)
     */
    private $taxRate;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="current_status", type="string", length=45, nullable=true)
     */
    private $currentStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer", nullable=true)
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="target_class", type="string", length=45, nullable=true)
     */
    private $targetClass;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $sourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="source_class", type="string", length=45, nullable=true)
     */
    private $sourceClass;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=45, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="change_on", type="datetime", nullable=true)
     */
    private $changeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="change_by", type="integer", nullable=true)
     */
    private $changeBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_number", type="integer", nullable=true)
     */
    private $revisionNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_posted", type="boolean", nullable=true)
     */
    private $isPosted;

    /**
     * @var float
     *
     * @ORM\Column(name="actual_quantity", type="float", precision=10, scale=4, nullable=true)
     */
    private $actualQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=45, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="stock_remarks", type="string", length=45, nullable=true)
     */
    private $stockRemarks;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=45, nullable=true)
     */
    private $transactionType;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_serial_id", type="integer", nullable=true)
     */
    private $itemSerialId;

    /**
     * @var string
     *
     * @ORM\Column(name="item_batch_id", type="string", length=45, nullable=true)
     */
    private $itemBatchId;

    /**
     * @var string
     *
     * @ORM\Column(name="cogs_local", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $cogsLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="cogs_doc", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $cogsDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_quantity", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedStandardQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedStandardUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_stock_quantity", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedStockQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_stock_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedStockUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_purchase_quantity", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedPurchaseQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="doc_quantity", type="float", precision=10, scale=4, nullable=true)
     */
    private $docQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_unit", type="string", length=45, nullable=true)
     */
    private $docUnit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversed", type="boolean", nullable=true)
     */
    private $isReversed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reversal_date", type="datetime", nullable=true)
     */
    private $reversalDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="reversal_doc", type="integer", nullable=true)
     */
    private $reversalDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="reversal_reason", type="string", length=100, nullable=true)
     */
    private $reversalReason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversable", type="boolean", nullable=true)
     */
    private $isReversable;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var string
     *
     * @ORM\Column(name="local_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $localUnitPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     * @var string
     *
     * @ORM\Column(name="mv_uuid", type="string", length=36, nullable=true)
     */
    private $mvUuid;

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
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var \Application\Entity\NmtProcurePr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     * })
     */
    private $pr;

    /**
     * @var \Application\Entity\NmtProcurePo
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     * })
     */
    private $po;

    /**
     * @var \Application\Entity\FinVendorInvoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_invoice_id", referencedColumnName="id")
     * })
     */
    private $vendorInvoice;

    /**
     * @var \Application\Entity\NmtProcurePoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_row_id", referencedColumnName="id")
     * })
     */
    private $poRow;

    /**
     * @var \Application\Entity\NmtProcureGrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_row_id", referencedColumnName="id")
     * })
     */
    private $grRow;

    /**
     * @var \Application\Entity\NmtInventoryGi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryGi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_gi_id", referencedColumnName="id")
     * })
     */
    private $inventoryGi;

    /**
     * @var \Application\Entity\NmtInventoryGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_gr_id", referencedColumnName="id")
     * })
     */
    private $inventoryGr;

    /**
     * @var \Application\Entity\NmtInventoryTransfer
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryTransfer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_transfer_id", referencedColumnName="id")
     * })
     */
    private $inventoryTransfer;

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
     * @var \Application\Entity\NmtProcureGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_id", referencedColumnName="id")
     * })
     */
    private $gr;

    /**
     * @var \Application\Entity\NmtInventoryMv
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryMv")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movement_id", referencedColumnName="id")
     * })
     */
    private $movement;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_currency_id", referencedColumnName="id")
     * })
     */
    private $docCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="local_currency_id", referencedColumnName="id")
     * })
     */
    private $localCurrency;

    /**
     * @var \Application\Entity\NmtPmProject
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtPmProject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @var \Application\Entity\FinCostCenter
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinCostCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cost_center_id", referencedColumnName="id")
     * })
     */
    private $costCenter;

    /**
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_uom", referencedColumnName="id")
     * })
     */
    private $docUom;

    /**
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posting_period_id", referencedColumnName="id")
     * })
     */
    private $postingPeriod;

    /**
     * @var \Application\Entity\NmtInventoryWarehouseLocation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouseLocation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wh_location", referencedColumnName="id")
     * })
     */
    private $whLocation;

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
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="invoice_row_id", referencedColumnName="id")
     * })
     */
    private $invoiceRow;



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
     * @param float $quantity
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
     * @return float
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
     * Set taxRate
     *
     * @param integer $taxRate
     *
     * @return NmtInventoryTrx
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get taxRate
     *
     * @return integer
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtInventoryTrx
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
     * Set currentStatus
     *
     * @param string $currentStatus
     *
     * @return NmtInventoryTrx
     */
    public function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;

        return $this;
    }

    /**
     * Get currentStatus
     *
     * @return string
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return NmtInventoryTrx
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set targetClass
     *
     * @param string $targetClass
     *
     * @return NmtInventoryTrx
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;

        return $this;
    }

    /**
     * Get targetClass
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return NmtInventoryTrx
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId
     *
     * @return integer
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set sourceClass
     *
     * @param string $sourceClass
     *
     * @return NmtInventoryTrx
     */
    public function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;

        return $this;
    }

    /**
     * Get sourceClass
     *
     * @return string
     */
    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtInventoryTrx
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;

        return $this;
    }

    /**
     * Get docStatus
     *
     * @return string
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryTrx
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
     * Set changeOn
     *
     * @param \DateTime $changeOn
     *
     * @return NmtInventoryTrx
     */
    public function setChangeOn($changeOn)
    {
        $this->changeOn = $changeOn;

        return $this;
    }

    /**
     * Get changeOn
     *
     * @return \DateTime
     */
    public function getChangeOn()
    {
        return $this->changeOn;
    }

    /**
     * Set changeBy
     *
     * @param integer $changeBy
     *
     * @return NmtInventoryTrx
     */
    public function setChangeBy($changeBy)
    {
        $this->changeBy = $changeBy;

        return $this;
    }

    /**
     * Get changeBy
     *
     * @return integer
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }

    /**
     * Set revisionNumber
     *
     * @param integer $revisionNumber
     *
     * @return NmtInventoryTrx
     */
    public function setRevisionNumber($revisionNumber)
    {
        $this->revisionNumber = $revisionNumber;

        return $this;
    }

    /**
     * Get revisionNumber
     *
     * @return integer
     */
    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    /**
     * Set isPosted
     *
     * @param boolean $isPosted
     *
     * @return NmtInventoryTrx
     */
    public function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;

        return $this;
    }

    /**
     * Get isPosted
     *
     * @return boolean
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     * Set actualQuantity
     *
     * @param float $actualQuantity
     *
     * @return NmtInventoryTrx
     */
    public function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity = $actualQuantity;

        return $this;
    }

    /**
     * Get actualQuantity
     *
     * @return float
     */
    public function getActualQuantity()
    {
        return $this->actualQuantity;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return NmtInventoryTrx
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * Get transactionStatus
     *
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Set stockRemarks
     *
     * @param string $stockRemarks
     *
     * @return NmtInventoryTrx
     */
    public function setStockRemarks($stockRemarks)
    {
        $this->stockRemarks = $stockRemarks;

        return $this;
    }

    /**
     * Get stockRemarks
     *
     * @return string
     */
    public function getStockRemarks()
    {
        return $this->stockRemarks;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return NmtInventoryTrx
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set itemSerialId
     *
     * @param integer $itemSerialId
     *
     * @return NmtInventoryTrx
     */
    public function setItemSerialId($itemSerialId)
    {
        $this->itemSerialId = $itemSerialId;

        return $this;
    }

    /**
     * Get itemSerialId
     *
     * @return integer
     */
    public function getItemSerialId()
    {
        return $this->itemSerialId;
    }

    /**
     * Set itemBatchId
     *
     * @param string $itemBatchId
     *
     * @return NmtInventoryTrx
     */
    public function setItemBatchId($itemBatchId)
    {
        $this->itemBatchId = $itemBatchId;

        return $this;
    }

    /**
     * Get itemBatchId
     *
     * @return string
     */
    public function getItemBatchId()
    {
        return $this->itemBatchId;
    }

    /**
     * Set cogsLocal
     *
     * @param string $cogsLocal
     *
     * @return NmtInventoryTrx
     */
    public function setCogsLocal($cogsLocal)
    {
        $this->cogsLocal = $cogsLocal;

        return $this;
    }

    /**
     * Get cogsLocal
     *
     * @return string
     */
    public function getCogsLocal()
    {
        return $this->cogsLocal;
    }

    /**
     * Set cogsDoc
     *
     * @param string $cogsDoc
     *
     * @return NmtInventoryTrx
     */
    public function setCogsDoc($cogsDoc)
    {
        $this->cogsDoc = $cogsDoc;

        return $this;
    }

    /**
     * Get cogsDoc
     *
     * @return string
     */
    public function getCogsDoc()
    {
        return $this->cogsDoc;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return NmtInventoryTrx
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Get exchangeRate
     *
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Set convertedStandardQuantity
     *
     * @param string $convertedStandardQuantity
     *
     * @return NmtInventoryTrx
     */
    public function setConvertedStandardQuantity($convertedStandardQuantity)
    {
        $this->convertedStandardQuantity = $convertedStandardQuantity;

        return $this;
    }

    /**
     * Get convertedStandardQuantity
     *
     * @return string
     */
    public function getConvertedStandardQuantity()
    {
        return $this->convertedStandardQuantity;
    }

    /**
     * Set convertedStandardUnitPrice
     *
     * @param string $convertedStandardUnitPrice
     *
     * @return NmtInventoryTrx
     */
    public function setConvertedStandardUnitPrice($convertedStandardUnitPrice)
    {
        $this->convertedStandardUnitPrice = $convertedStandardUnitPrice;

        return $this;
    }

    /**
     * Get convertedStandardUnitPrice
     *
     * @return string
     */
    public function getConvertedStandardUnitPrice()
    {
        return $this->convertedStandardUnitPrice;
    }

    /**
     * Set convertedStockQuantity
     *
     * @param string $convertedStockQuantity
     *
     * @return NmtInventoryTrx
     */
    public function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;

        return $this;
    }

    /**
     * Get convertedStockQuantity
     *
     * @return string
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     * Set convertedStockUnitPrice
     *
     * @param string $convertedStockUnitPrice
     *
     * @return NmtInventoryTrx
     */
    public function setConvertedStockUnitPrice($convertedStockUnitPrice)
    {
        $this->convertedStockUnitPrice = $convertedStockUnitPrice;

        return $this;
    }

    /**
     * Get convertedStockUnitPrice
     *
     * @return string
     */
    public function getConvertedStockUnitPrice()
    {
        return $this->convertedStockUnitPrice;
    }

    /**
     * Set convertedPurchaseQuantity
     *
     * @param string $convertedPurchaseQuantity
     *
     * @return NmtInventoryTrx
     */
    public function setConvertedPurchaseQuantity($convertedPurchaseQuantity)
    {
        $this->convertedPurchaseQuantity = $convertedPurchaseQuantity;

        return $this;
    }

    /**
     * Get convertedPurchaseQuantity
     *
     * @return string
     */
    public function getConvertedPurchaseQuantity()
    {
        return $this->convertedPurchaseQuantity;
    }

    /**
     * Set docQuantity
     *
     * @param float $docQuantity
     *
     * @return NmtInventoryTrx
     */
    public function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;

        return $this;
    }

    /**
     * Get docQuantity
     *
     * @return float
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     * Set docUnitPrice
     *
     * @param string $docUnitPrice
     *
     * @return NmtInventoryTrx
     */
    public function setDocUnitPrice($docUnitPrice)
    {
        $this->docUnitPrice = $docUnitPrice;

        return $this;
    }

    /**
     * Get docUnitPrice
     *
     * @return string
     */
    public function getDocUnitPrice()
    {
        return $this->docUnitPrice;
    }

    /**
     * Set docUnit
     *
     * @param string $docUnit
     *
     * @return NmtInventoryTrx
     */
    public function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;

        return $this;
    }

    /**
     * Get docUnit
     *
     * @return string
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return NmtInventoryTrx
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    /**
     * Get isReversed
     *
     * @return boolean
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * Set reversalDate
     *
     * @param \DateTime $reversalDate
     *
     * @return NmtInventoryTrx
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;

        return $this;
    }

    /**
     * Get reversalDate
     *
     * @return \DateTime
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * Set reversalDoc
     *
     * @param integer $reversalDoc
     *
     * @return NmtInventoryTrx
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;

        return $this;
    }

    /**
     * Get reversalDoc
     *
     * @return integer
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     * Set reversalReason
     *
     * @param string $reversalReason
     *
     * @return NmtInventoryTrx
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;

        return $this;
    }

    /**
     * Get reversalReason
     *
     * @return string
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * Set isReversable
     *
     * @param boolean $isReversable
     *
     * @return NmtInventoryTrx
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;

        return $this;
    }

    /**
     * Get isReversable
     *
     * @return boolean
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     * Set docType
     *
     * @param string $docType
     *
     * @return NmtInventoryTrx
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;

        return $this;
    }

    /**
     * Get docType
     *
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * Set localUnitPrice
     *
     * @param string $localUnitPrice
     *
     * @return NmtInventoryTrx
     */
    public function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;

        return $this;
    }

    /**
     * Get localUnitPrice
     *
     * @return string
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return NmtInventoryTrx
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;

        return $this;
    }

    /**
     * Get reversalBlocked
     *
     * @return boolean
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     * Set mvUuid
     *
     * @param string $mvUuid
     *
     * @return NmtInventoryTrx
     */
    public function setMvUuid($mvUuid)
    {
        $this->mvUuid = $mvUuid;

        return $this;
    }

    /**
     * Get mvUuid
     *
     * @return string
     */
    public function getMvUuid()
    {
        return $this->mvUuid;
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryTrx
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
     * Set pr
     *
     * @param \Application\Entity\NmtProcurePr $pr
     *
     * @return NmtInventoryTrx
     */
    public function setPr(\Application\Entity\NmtProcurePr $pr = null)
    {
        $this->pr = $pr;

        return $this;
    }

    /**
     * Get pr
     *
     * @return \Application\Entity\NmtProcurePr
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * Set po
     *
     * @param \Application\Entity\NmtProcurePo $po
     *
     * @return NmtInventoryTrx
     */
    public function setPo(\Application\Entity\NmtProcurePo $po = null)
    {
        $this->po = $po;

        return $this;
    }

    /**
     * Get po
     *
     * @return \Application\Entity\NmtProcurePo
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     * Set vendorInvoice
     *
     * @param \Application\Entity\FinVendorInvoice $vendorInvoice
     *
     * @return NmtInventoryTrx
     */
    public function setVendorInvoice(\Application\Entity\FinVendorInvoice $vendorInvoice = null)
    {
        $this->vendorInvoice = $vendorInvoice;

        return $this;
    }

    /**
     * Get vendorInvoice
     *
     * @return \Application\Entity\FinVendorInvoice
     */
    public function getVendorInvoice()
    {
        return $this->vendorInvoice;
    }

    /**
     * Set poRow
     *
     * @param \Application\Entity\NmtProcurePoRow $poRow
     *
     * @return NmtInventoryTrx
     */
    public function setPoRow(\Application\Entity\NmtProcurePoRow $poRow = null)
    {
        $this->poRow = $poRow;

        return $this;
    }

    /**
     * Get poRow
     *
     * @return \Application\Entity\NmtProcurePoRow
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return NmtInventoryTrx
     */
    public function setGrRow(\Application\Entity\NmtProcureGrRow $grRow = null)
    {
        $this->grRow = $grRow;

        return $this;
    }

    /**
     * Get grRow
     *
     * @return \Application\Entity\NmtProcureGrRow
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     * Set inventoryGi
     *
     * @param \Application\Entity\NmtInventoryGi $inventoryGi
     *
     * @return NmtInventoryTrx
     */
    public function setInventoryGi(\Application\Entity\NmtInventoryGi $inventoryGi = null)
    {
        $this->inventoryGi = $inventoryGi;

        return $this;
    }

    /**
     * Get inventoryGi
     *
     * @return \Application\Entity\NmtInventoryGi
     */
    public function getInventoryGi()
    {
        return $this->inventoryGi;
    }

    /**
     * Set inventoryGr
     *
     * @param \Application\Entity\NmtInventoryGr $inventoryGr
     *
     * @return NmtInventoryTrx
     */
    public function setInventoryGr(\Application\Entity\NmtInventoryGr $inventoryGr = null)
    {
        $this->inventoryGr = $inventoryGr;

        return $this;
    }

    /**
     * Get inventoryGr
     *
     * @return \Application\Entity\NmtInventoryGr
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }

    /**
     * Set inventoryTransfer
     *
     * @param \Application\Entity\NmtInventoryTransfer $inventoryTransfer
     *
     * @return NmtInventoryTrx
     */
    public function setInventoryTransfer(\Application\Entity\NmtInventoryTransfer $inventoryTransfer = null)
    {
        $this->inventoryTransfer = $inventoryTransfer;

        return $this;
    }

    /**
     * Get inventoryTransfer
     *
     * @return \Application\Entity\NmtInventoryTransfer
     */
    public function getInventoryTransfer()
    {
        return $this->inventoryTransfer;
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
     * Set gr
     *
     * @param \Application\Entity\NmtProcureGr $gr
     *
     * @return NmtInventoryTrx
     */
    public function setGr(\Application\Entity\NmtProcureGr $gr = null)
    {
        $this->gr = $gr;

        return $this;
    }

    /**
     * Get gr
     *
     * @return \Application\Entity\NmtProcureGr
     */
    public function getGr()
    {
        return $this->gr;
    }

    /**
     * Set movement
     *
     * @param \Application\Entity\NmtInventoryMv $movement
     *
     * @return NmtInventoryTrx
     */
    public function setMovement(\Application\Entity\NmtInventoryMv $movement = null)
    {
        $this->movement = $movement;

        return $this;
    }

    /**
     * Get movement
     *
     * @return \Application\Entity\NmtInventoryMv
     */
    public function getMovement()
    {
        return $this->movement;
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

    /**
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return NmtInventoryTrx
     */
    public function setDocCurrency(\Application\Entity\NmtApplicationCurrency $docCurrency = null)
    {
        $this->docCurrency = $docCurrency;

        return $this;
    }

    /**
     * Get docCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return NmtInventoryTrx
     */
    public function setLocalCurrency(\Application\Entity\NmtApplicationCurrency $localCurrency = null)
    {
        $this->localCurrency = $localCurrency;

        return $this;
    }

    /**
     * Get localCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     * Set project
     *
     * @param \Application\Entity\NmtPmProject $project
     *
     * @return NmtInventoryTrx
     */
    public function setProject(\Application\Entity\NmtPmProject $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\Entity\NmtPmProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set costCenter
     *
     * @param \Application\Entity\FinCostCenter $costCenter
     *
     * @return NmtInventoryTrx
     */
    public function setCostCenter(\Application\Entity\FinCostCenter $costCenter = null)
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * Get costCenter
     *
     * @return \Application\Entity\FinCostCenter
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Set docUom
     *
     * @param \Application\Entity\NmtApplicationUom $docUom
     *
     * @return NmtInventoryTrx
     */
    public function setDocUom(\Application\Entity\NmtApplicationUom $docUom = null)
    {
        $this->docUom = $docUom;

        return $this;
    }

    /**
     * Get docUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return NmtInventoryTrx
     */
    public function setPostingPeriod(\Application\Entity\NmtFinPostingPeriod $postingPeriod = null)
    {
        $this->postingPeriod = $postingPeriod;

        return $this;
    }

    /**
     * Get postingPeriod
     *
     * @return \Application\Entity\NmtFinPostingPeriod
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     * Set whLocation
     *
     * @param \Application\Entity\NmtInventoryWarehouseLocation $whLocation
     *
     * @return NmtInventoryTrx
     */
    public function setWhLocation(\Application\Entity\NmtInventoryWarehouseLocation $whLocation = null)
    {
        $this->whLocation = $whLocation;

        return $this;
    }

    /**
     * Get whLocation
     *
     * @return \Application\Entity\NmtInventoryWarehouseLocation
     */
    public function getWhLocation()
    {
        return $this->whLocation;
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
     * Set invoiceRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $invoiceRow
     *
     * @return NmtInventoryTrx
     */
    public function setInvoiceRow(\Application\Entity\FinVendorInvoiceRow $invoiceRow = null)
    {
        $this->invoiceRow = $invoiceRow;

        return $this;
    }

    /**
     * Get invoiceRow
     *
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    public function getInvoiceRow()
    {
        return $this->invoiceRow;
    }
}
