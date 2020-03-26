<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinVendorInvoiceRow
 *
 * @ORM\Table(name="fin_vendor_invoice_row", indexes={@ORM\Index(name="fin_vendor_invoice_row_FK1_idx", columns={"invoice_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK3_idx", columns={"pr_row_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK4_idx", columns={"created_by"}), @ORM\Index(name="fin_vendor_invoice_row_FK5_idx", columns={"warehouse_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK6_idx", columns={"lastchange_by"}), @ORM\Index(name="fin_vendor_invoice_row_FK7_idx", columns={"po_row_id"}), @ORM\Index(name="fin_vendor_invoice_row_IDX1", columns={"current_state"}), @ORM\Index(name="fin_vendor_invoice_row_IDX2", columns={"is_active"}), @ORM\Index(name="fin_vendor_invoice_row_FK10_idx", columns={"GL_account_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK11_idx", columns={"cost_center_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK12_idx", columns={"doc_uom"}), @ORM\Index(name="fin_vendor_invoice_row_FK13_idx", columns={"gr_row_id"}), @ORM\Index(name="fin_vendor_invoice_row_FK14_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class FinVendorInvoiceRow
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
     * @ORM\Column(name="row_number", type="integer", nullable=true)
     */
    private $rowNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

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
     * @ORM\Column(name="remarks", type="string", length=100, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="row_identifer", type="string", length=45, nullable=true)
     */
    private $rowIdentifer;

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
     * @ORM\Column(name="local_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $localUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $docUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="exw_unit_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $exwUnitPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="exw_currency", type="integer", nullable=true)
     */
    private $exwCurrency;

    /**
     * @var string
     *
     * @ORM\Column(name="local_net_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $localNetAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="local_gross_amount", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $localGrossAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=45, nullable=true)
     */
    private $transactionType;

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
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="total_exw_price", type="decimal", precision=15, scale=4, nullable=true)
     */
    private $totalExwPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="convert_factor_purchase", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertFactorPurchase;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_purchase_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedPurchaseQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_stock_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStockQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_stock_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStockUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStandardQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="converted_standard_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStandardUnitPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="doc_quantity", type="float", precision=10, scale=0, nullable=true)
     */
    private $docQuantity;

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
     * @ORM\Column(name="description_text", type="text", length=65535, nullable=true)
     */
    private $descriptionText;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_item_name", type="string", length=100, nullable=true)
     */
    private $vendorItemName;

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
     * @var \Application\Entity\NmtProcureGrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_row_id", referencedColumnName="id")
     * })
     */
    private $grRow;

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
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;

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
     * Set rowNumber
     *
     * @param integer $rowNumber
     *
     * @return FinVendorInvoiceRow
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
     * Set token
     *
     * @param string $token
     *
     * @return FinVendorInvoiceRow
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
     * Set quantity
     *
     * @param float $quantity
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set rowIdentifer
     *
     * @param string $rowIdentifer
     *
     * @return FinVendorInvoiceRow
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
     * Set discountRate
     *
     * @param integer $discountRate
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set localUnitPrice
     *
     * @param string $localUnitPrice
     *
     * @return FinVendorInvoiceRow
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
     * Set docUnitPrice
     *
     * @param string $docUnitPrice
     *
     * @return FinVendorInvoiceRow
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
     * Set exwUnitPrice
     *
     * @param string $exwUnitPrice
     *
     * @return FinVendorInvoiceRow
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
     * Set exwCurrency
     *
     * @param integer $exwCurrency
     *
     * @return FinVendorInvoiceRow
     */
    public function setExwCurrency($exwCurrency)
    {
        $this->exwCurrency = $exwCurrency;

        return $this;
    }

    /**
     * Get exwCurrency
     *
     * @return integer
     */
    public function getExwCurrency()
    {
        return $this->exwCurrency;
    }

    /**
     * Set localNetAmount
     *
     * @param string $localNetAmount
     *
     * @return FinVendorInvoiceRow
     */
    public function setLocalNetAmount($localNetAmount)
    {
        $this->localNetAmount = $localNetAmount;

        return $this;
    }

    /**
     * Get localNetAmount
     *
     * @return string
     */
    public function getLocalNetAmount()
    {
        return $this->localNetAmount;
    }

    /**
     * Set localGrossAmount
     *
     * @param string $localGrossAmount
     *
     * @return FinVendorInvoiceRow
     */
    public function setLocalGrossAmount($localGrossAmount)
    {
        $this->localGrossAmount = $localGrossAmount;

        return $this;
    }

    /**
     * Get localGrossAmount
     *
     * @return string
     */
    public function getLocalGrossAmount()
    {
        return $this->localGrossAmount;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return FinVendorInvoiceRow
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
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return FinVendorInvoiceRow
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
     * Set totalExwPrice
     *
     * @param string $totalExwPrice
     *
     * @return FinVendorInvoiceRow
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
     * Set convertFactorPurchase
     *
     * @param string $convertFactorPurchase
     *
     * @return FinVendorInvoiceRow
     */
    public function setConvertFactorPurchase($convertFactorPurchase)
    {
        $this->convertFactorPurchase = $convertFactorPurchase;

        return $this;
    }

    /**
     * Get convertFactorPurchase
     *
     * @return string
     */
    public function getConvertFactorPurchase()
    {
        return $this->convertFactorPurchase;
    }

    /**
     * Set convertedPurchaseQuantity
     *
     * @param string $convertedPurchaseQuantity
     *
     * @return FinVendorInvoiceRow
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
     * Set convertedStockQuantity
     *
     * @param string $convertedStockQuantity
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set convertedStandardQuantity
     *
     * @param string $convertedStandardQuantity
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set docQuantity
     *
     * @param float $docQuantity
     *
     * @return FinVendorInvoiceRow
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
     * Set docUnit
     *
     * @param string $docUnit
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set isReversable
     *
     * @param boolean $isReversable
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set descriptionText
     *
     * @param string $descriptionText
     *
     * @return FinVendorInvoiceRow
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
     * Set vendorItemName
     *
     * @param string $vendorItemName
     *
     * @return FinVendorInvoiceRow
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
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return FinVendorInvoiceRow
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * @return FinVendorInvoiceRow
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
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return FinVendorInvoiceRow
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     * Set poRow
     *
     * @param \Application\Entity\NmtProcurePoRow $poRow
     *
     * @return FinVendorInvoiceRow
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
