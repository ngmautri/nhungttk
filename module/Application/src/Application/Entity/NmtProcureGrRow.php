<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureGrRow
 *
 * @ORM\Table(name="nmt_procure_gr_row", indexes={@ORM\Index(name="nmt_procure_gr_row_FK1_idx", columns={"invoice_id"}), @ORM\Index(name="nmt_procure_gr_row_FK3_idx", columns={"pr_row_id"}), @ORM\Index(name="nmt_procure_gr_row_FK4_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_gr_row_FK5_idx", columns={"warehouse_id"}), @ORM\Index(name="nmt_procure_gr_row_FK6_idx", columns={"lastchanged_by"}), @ORM\Index(name="nmt_procure_gr_row_IDX1", columns={"current_state"}), @ORM\Index(name="nmt_procure_gr_row_FK8_idx", columns={"item_id"}), @ORM\Index(name="nmt_procure_gr_row_FK9_idx", columns={"po_row_id"}), @ORM\Index(name="nmt_procure_gr_row_FK10_idx", columns={"gr_id"}), @ORM\Index(name="nmt_procure_gr_row_IDX2", columns={"token"}), @ORM\Index(name="nmt_procure_gr_row_FK11_idx", columns={"ap_invoice_row_id"}), @ORM\Index(name="nmt_procure_gr_row_FK12_idx", columns={"GL_account_id"}), @ORM\Index(name="nmt_procure_gr_row_FK13_idx", columns={"cost_center_id"}), @ORM\Index(name="nmt_procure_gr_row_FK14_idx", columns={"doc_uom"})})
 * @ORM\Entity
 */
class NmtProcureGrRow
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
     * @var integer
     *
     * @ORM\Column(name="row_number", type="integer", nullable=true)
     */
    private $rowNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="row_identifer", type="string", length=45, nullable=true)
     */
    private $rowIdentifer;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=4, nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_price", type="decimal", precision=15, scale=4, nullable=false)
     */
    private $unitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="net_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $netAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=45, nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="item_unit", type="string", length=45, nullable=true)
     */
    private $itemUnit;

    /**
     * @var float
     *
     * @ORM\Column(name="conversion_factor", type="float", precision=10, scale=4, nullable=true)
     */
    private $conversionFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="converstion_text", type="string", length=45, nullable=true)
     */
    private $converstionText;

    /**
     * @var integer
     *
     * @ORM\Column(name="tax_rate", type="integer", nullable=true)
     */
    private $taxRate;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
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
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_code", type="string", length=45, nullable=true)
     */
    private $vendorItemCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="trace_stock", type="boolean", nullable=true)
     */
    private $traceStock;

    /**
     * @var string
     *
     * @ORM\Column(name="gross_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $grossAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $taxAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="fa_remarks", type="string", length=200, nullable=true)
     */
    private $faRemarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="discount_rate", type="integer", nullable=true)
     */
    private $discountRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="target_object", type="string", length=255, nullable=true)
     */
    private $targetObject;

    /**
     * @var string
     *
     * @ORM\Column(name="source_object", type="string", length=255, nullable=true)
     */
    private $sourceObject;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_object_id", type="integer", nullable=true)
     */
    private $targetObjectId;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_object_id", type="integer", nullable=true)
     */
    private $sourceObjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_posted", type="boolean", nullable=true)
     */
    private $isPosted;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gr_date", type="datetime", nullable=true)
     */
    private $grDate;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=30, nullable=true)
     */
    private $transactionType;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="exw_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $exwUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="total_exw_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalExwPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_purchase_quantity", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedPurchaseQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_quantity", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $convertedStandardQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_unit_price", type="decimal", precision=14, scale=4, nullable=true)
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
     * @var float
     *
     * @ORM\Column(name="doc_quantity", type="float", precision=10, scale=0, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="converted_purchase_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedPurchaseUnitPrice;

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
     * @var string
     *
     * @ORM\Column(name="reversal_reason", type="string", length=100, nullable=true)
     */
    private $reversalReason;

    /**
     * @var integer
     *
     * @ORM\Column(name="reversal_doc", type="integer", nullable=true)
     */
    private $reversalDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="flow", type="string", nullable=true)
     */
    private $flow;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_name", type="string", length=100, nullable=true)
     */
    private $vendorItemName;

    /**
     * @var string
     *
     * @ORM\Column(name="description_text", type="text", length=65535, nullable=true)
     */
    private $descriptionText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     * @var \Application\Entity\FinVendorInvoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     * })
     */
    private $invoice;

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
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_invoice_row_id", referencedColumnName="id")
     * })
     */
    private $apInvoiceRow;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;

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
     * @var \Application\Entity\NmtProcurePrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_row_id", referencedColumnName="id")
     * })
     */
    private $prRow;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchanged_by", referencedColumnName="id")
     * })
     */
    private $lastchangedBy;

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
     * @var \Application\Entity\NmtProcurePoRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePoRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_row_id", referencedColumnName="id")
     * })
     */
    private $poRow;



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
     * @return NmtProcureGrRow
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
     * Set rowNumber
     *
     * @param integer $rowNumber
     *
     * @return NmtProcureGrRow
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    /**
     * Get rowNumber
     *
     * @return integer
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Set rowIdentifer
     *
     * @param string $rowIdentifer
     *
     * @return NmtProcureGrRow
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;

        return $this;
    }

    /**
     * Get rowIdentifer
     *
     * @return string
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     *
     * @return NmtProcureGrRow
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
     * Set unitPrice
     *
     * @param string $unitPrice
     *
     * @return NmtProcureGrRow
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return string
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set netAmount
     *
     * @param string $netAmount
     *
     * @return NmtProcureGrRow
     */
    public function setNetAmount($netAmount)
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    /**
     * Get netAmount
     *
     * @return string
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return NmtProcureGrRow
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set itemUnit
     *
     * @param string $itemUnit
     *
     * @return NmtProcureGrRow
     */
    public function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;

        return $this;
    }

    /**
     * Get itemUnit
     *
     * @return string
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     * Set conversionFactor
     *
     * @param float $conversionFactor
     *
     * @return NmtProcureGrRow
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;

        return $this;
    }

    /**
     * Get conversionFactor
     *
     * @return float
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * Set converstionText
     *
     * @param string $converstionText
     *
     * @return NmtProcureGrRow
     */
    public function setConverstionText($converstionText)
    {
        $this->converstionText = $converstionText;

        return $this;
    }

    /**
     * Get converstionText
     *
     * @return string
     */
    public function getConverstionText()
    {
        return $this->converstionText;
    }

    /**
     * Set taxRate
     *
     * @param integer $taxRate
     *
     * @return NmtProcureGrRow
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtProcureGrRow
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtProcureGrRow
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
     * Set vendorItemCode
     *
     * @param string $vendorItemCode
     *
     * @return NmtProcureGrRow
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
     * Set traceStock
     *
     * @param boolean $traceStock
     *
     * @return NmtProcureGrRow
     */
    public function setTraceStock($traceStock)
    {
        $this->traceStock = $traceStock;

        return $this;
    }

    /**
     * Get traceStock
     *
     * @return boolean
     */
    public function getTraceStock()
    {
        return $this->traceStock;
    }

    /**
     * Set grossAmount
     *
     * @param string $grossAmount
     *
     * @return NmtProcureGrRow
     */
    public function setGrossAmount($grossAmount)
    {
        $this->grossAmount = $grossAmount;

        return $this;
    }

    /**
     * Get grossAmount
     *
     * @return string
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * Set taxAmount
     *
     * @param string $taxAmount
     *
     * @return NmtProcureGrRow
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    /**
     * Get taxAmount
     *
     * @return string
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * Set faRemarks
     *
     * @param string $faRemarks
     *
     * @return NmtProcureGrRow
     */
    public function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;

        return $this;
    }

    /**
     * Get faRemarks
     *
     * @return string
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     * Set discountRate
     *
     * @param integer $discountRate
     *
     * @return NmtProcureGrRow
     */
    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;

        return $this;
    }

    /**
     * Get discountRate
     *
     * @return integer
     */
    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtProcureGrRow
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
     * Set targetObject
     *
     * @param string $targetObject
     *
     * @return NmtProcureGrRow
     */
    public function setTargetObject($targetObject)
    {
        $this->targetObject = $targetObject;

        return $this;
    }

    /**
     * Get targetObject
     *
     * @return string
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     * Set sourceObject
     *
     * @param string $sourceObject
     *
     * @return NmtProcureGrRow
     */
    public function setSourceObject($sourceObject)
    {
        $this->sourceObject = $sourceObject;

        return $this;
    }

    /**
     * Get sourceObject
     *
     * @return string
     */
    public function getSourceObject()
    {
        return $this->sourceObject;
    }

    /**
     * Set targetObjectId
     *
     * @param integer $targetObjectId
     *
     * @return NmtProcureGrRow
     */
    public function setTargetObjectId($targetObjectId)
    {
        $this->targetObjectId = $targetObjectId;

        return $this;
    }

    /**
     * Get targetObjectId
     *
     * @return integer
     */
    public function getTargetObjectId()
    {
        return $this->targetObjectId;
    }

    /**
     * Set sourceObjectId
     *
     * @param integer $sourceObjectId
     *
     * @return NmtProcureGrRow
     */
    public function setSourceObjectId($sourceObjectId)
    {
        $this->sourceObjectId = $sourceObjectId;

        return $this;
    }

    /**
     * Get sourceObjectId
     *
     * @return integer
     */
    public function getSourceObjectId()
    {
        return $this->sourceObjectId;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtProcureGrRow
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtProcureGrRow
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
     * Set isPosted
     *
     * @param boolean $isPosted
     *
     * @return NmtProcureGrRow
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
     * Set grDate
     *
     * @param \DateTime $grDate
     *
     * @return NmtProcureGrRow
     */
    public function setGrDate($grDate)
    {
        $this->grDate = $grDate;

        return $this;
    }

    /**
     * Get grDate
     *
     * @return \DateTime
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return NmtProcureGrRow
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;

        return $this;
    }

    /**
     * Get workflowStatus
     *
     * @return string
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return NmtProcureGrRow
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
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return NmtProcureGrRow
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
     * Set exwUnitPrice
     *
     * @param string $exwUnitPrice
     *
     * @return NmtProcureGrRow
     */
    public function setExwUnitPrice($exwUnitPrice)
    {
        $this->exwUnitPrice = $exwUnitPrice;

        return $this;
    }

    /**
     * Get exwUnitPrice
     *
     * @return string
     */
    public function getExwUnitPrice()
    {
        return $this->exwUnitPrice;
    }

    /**
     * Set totalExwPrice
     *
     * @param string $totalExwPrice
     *
     * @return NmtProcureGrRow
     */
    public function setTotalExwPrice($totalExwPrice)
    {
        $this->totalExwPrice = $totalExwPrice;

        return $this;
    }

    /**
     * Get totalExwPrice
     *
     * @return string
     */
    public function getTotalExwPrice()
    {
        return $this->totalExwPrice;
    }

    /**
     * Set convertedPurchaseQuantity
     *
     * @param string $convertedPurchaseQuantity
     *
     * @return NmtProcureGrRow
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
     * Set convertedStandardQuantity
     *
     * @param string $convertedStandardQuantity
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set docQuantity
     *
     * @param float $docQuantity
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set convertedPurchaseUnitPrice
     *
     * @param string $convertedPurchaseUnitPrice
     *
     * @return NmtProcureGrRow
     */
    public function setConvertedPurchaseUnitPrice($convertedPurchaseUnitPrice)
    {
        $this->convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice;

        return $this;
    }

    /**
     * Get convertedPurchaseUnitPrice
     *
     * @return string
     */
    public function getConvertedPurchaseUnitPrice()
    {
        return $this->convertedPurchaseUnitPrice;
    }

    /**
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set reversalReason
     *
     * @param string $reversalReason
     *
     * @return NmtProcureGrRow
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
     * Set reversalDoc
     *
     * @param integer $reversalDoc
     *
     * @return NmtProcureGrRow
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
     * Set flow
     *
     * @param string $flow
     *
     * @return NmtProcureGrRow
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
     * Set docType
     *
     * @param string $docType
     *
     * @return NmtProcureGrRow
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
     * Set vendorItemName
     *
     * @param string $vendorItemName
     *
     * @return NmtProcureGrRow
     */
    public function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;

        return $this;
    }

    /**
     * Get vendorItemName
     *
     * @return string
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     * Set descriptionText
     *
     * @param string $descriptionText
     *
     * @return NmtProcureGrRow
     */
    public function setDescriptionText($descriptionText)
    {
        $this->descriptionText = $descriptionText;

        return $this;
    }

    /**
     * Get descriptionText
     *
     * @return string
     */
    public function getDescriptionText()
    {
        return $this->descriptionText;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return NmtProcureGrRow
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
     * Set invoice
     *
     * @param \Application\Entity\FinVendorInvoice $invoice
     *
     * @return NmtProcureGrRow
     */
    public function setInvoice(\Application\Entity\FinVendorInvoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Application\Entity\FinVendorInvoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set gr
     *
     * @param \Application\Entity\NmtProcureGr $gr
     *
     * @return NmtProcureGrRow
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
     * Set apInvoiceRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apInvoiceRow
     *
     * @return NmtProcureGrRow
     */
    public function setApInvoiceRow(\Application\Entity\FinVendorInvoiceRow $apInvoiceRow = null)
    {
        $this->apInvoiceRow = $apInvoiceRow;

        return $this;
    }

    /**
     * Get apInvoiceRow
     *
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    public function getApInvoiceRow()
    {
        return $this->apInvoiceRow;
    }

    /**
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return NmtProcureGrRow
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * Set costCenter
     *
     * @param \Application\Entity\FinCostCenter $costCenter
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set prRow
     *
     * @param \Application\Entity\NmtProcurePrRow $prRow
     *
     * @return NmtProcureGrRow
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcureGrRow
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
     * @return NmtProcureGrRow
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
     * Set lastchangedBy
     *
     * @param \Application\Entity\MlaUsers $lastchangedBy
     *
     * @return NmtProcureGrRow
     */
    public function setLastchangedBy(\Application\Entity\MlaUsers $lastchangedBy = null)
    {
        $this->lastchangedBy = $lastchangedBy;

        return $this;
    }

    /**
     * Get lastchangedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangedBy()
    {
        return $this->lastchangedBy;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtProcureGrRow
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
     * Set poRow
     *
     * @param \Application\Entity\NmtProcurePoRow $poRow
     *
     * @return NmtProcureGrRow
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
}
