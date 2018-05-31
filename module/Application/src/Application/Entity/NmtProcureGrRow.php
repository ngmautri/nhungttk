<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureGrRow
 *
 * @ORM\Table(name="nmt_procure_gr_row", indexes={@ORM\Index(name="nmt_procure_gr_row_FK1_idx", columns={"invoice_id"}), @ORM\Index(name="nmt_procure_gr_row_FK3_idx", columns={"pr_row_id"}), @ORM\Index(name="nmt_procure_gr_row_FK4_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_gr_row_FK5_idx", columns={"warehouse_id"}), @ORM\Index(name="nmt_procure_gr_row_FK6_idx", columns={"lastchanged_by"}), @ORM\Index(name="nmt_procure_gr_row_IDX1", columns={"current_state"}), @ORM\Index(name="nmt_procure_gr_row_FK8_idx", columns={"item_id"}), @ORM\Index(name="nmt_procure_gr_row_FK9_idx", columns={"po_row_id"}), @ORM\Index(name="nmt_procure_gr_row_FK10_idx", columns={"gr_id"}), @ORM\Index(name="nmt_procure_gr_row_IDX2", columns={"token"}), @ORM\Index(name="nmt_procure_gr_row_FK11_idx", columns={"ap_invoice_row_id"})})
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
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=true)
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
     * @param integer $quantity
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
     * @return integer
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
     * @param string $conversionFactor
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
     * @return string
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
