<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaDeliveryItems
 *
 * @ORM\Table(name="mla_delivery_items", indexes={@ORM\Index(name="mla_delivery_items_FK1_idx", columns={"delivery_id"}), @ORM\Index(name="mla_delivery_items_FK1_idx1", columns={"pr_item_id"}), @ORM\Index(name="mla_delivery_items_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="mla_delivery_items_FK4_idx", columns={"created_by"}), @ORM\Index(name="mla_delivery_items_FK5_idx", columns={"last_workflow_id"}), @ORM\Index(name="mla_delivery_items_FK5_idx1", columns={"po_item_id"})})
 * @ORM\Entity
 */
class MlaDeliveryItems
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
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receipt_date", type="datetime", nullable=true)
     */
    private $receiptDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="po_item_id", type="integer", nullable=false)
     */
    private $poItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=45, nullable=true)
     */
    private $unit;

    /**
     * @var integer
     *
     * @ORM\Column(name="delivered_quantity", type="integer", nullable=false)
     */
    private $deliveredQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", nullable=true)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="created_on", type="string", length=45, nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_no", type="string", length=100, nullable=true)
     */
    private $invoiceNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="invoice_date", type="datetime", nullable=true)
     */
    private $invoiceDate;

    /**
     * @var \Application\Entity\MlaPurchaseRequestItems
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaPurchaseRequestItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_item_id", referencedColumnName="id")
     * })
     */
    private $prItem;

    /**
     * @var \Application\Entity\MlaDelivery
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDelivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
     * })
     */
    private $delivery;

    /**
     * @var \Application\Entity\MlaVendors
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaVendors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     * })
     */
    private $vendor;

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
     * @var \Application\Entity\MlaDeliveryItemsWorkflows
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDeliveryItemsWorkflows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_workflow_id", referencedColumnName="id")
     * })
     */
    private $lastWorkflow;



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
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     *
     * @return MlaDeliveryItems
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * Set receiptDate
     *
     * @param \DateTime $receiptDate
     *
     * @return MlaDeliveryItems
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;

        return $this;
    }

    /**
     * Get receiptDate
     *
     * @return \DateTime
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * Set poItemId
     *
     * @param integer $poItemId
     *
     * @return MlaDeliveryItems
     */
    public function setPoItemId($poItemId)
    {
        $this->poItemId = $poItemId;

        return $this;
    }

    /**
     * Get poItemId
     *
     * @return integer
     */
    public function getPoItemId()
    {
        return $this->poItemId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MlaDeliveryItems
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return MlaDeliveryItems
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return MlaDeliveryItems
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
     * Set deliveredQuantity
     *
     * @param integer $deliveredQuantity
     *
     * @return MlaDeliveryItems
     */
    public function setDeliveredQuantity($deliveredQuantity)
    {
        $this->deliveredQuantity = $deliveredQuantity;

        return $this;
    }

    /**
     * Get deliveredQuantity
     *
     * @return integer
     */
    public function getDeliveredQuantity()
    {
        return $this->deliveredQuantity;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return MlaDeliveryItems
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return MlaDeliveryItems
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return MlaDeliveryItems
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set createdOn
     *
     * @param string $createdOn
     *
     * @return MlaDeliveryItems
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return string
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return MlaDeliveryItems
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
     * Set invoiceNo
     *
     * @param string $invoiceNo
     *
     * @return MlaDeliveryItems
     */
    public function setInvoiceNo($invoiceNo)
    {
        $this->invoiceNo = $invoiceNo;

        return $this;
    }

    /**
     * Get invoiceNo
     *
     * @return string
     */
    public function getInvoiceNo()
    {
        return $this->invoiceNo;
    }

    /**
     * Set invoiceDate
     *
     * @param \DateTime $invoiceDate
     *
     * @return MlaDeliveryItems
     */
    public function setInvoiceDate($invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    /**
     * Get invoiceDate
     *
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * Set prItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $prItem
     *
     * @return MlaDeliveryItems
     */
    public function setPrItem(\Application\Entity\MlaPurchaseRequestItems $prItem = null)
    {
        $this->prItem = $prItem;

        return $this;
    }

    /**
     * Get prItem
     *
     * @return \Application\Entity\MlaPurchaseRequestItems
     */
    public function getPrItem()
    {
        return $this->prItem;
    }

    /**
     * Set delivery
     *
     * @param \Application\Entity\MlaDelivery $delivery
     *
     * @return MlaDeliveryItems
     */
    public function setDelivery(\Application\Entity\MlaDelivery $delivery = null)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return \Application\Entity\MlaDelivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set vendor
     *
     * @param \Application\Entity\MlaVendors $vendor
     *
     * @return MlaDeliveryItems
     */
    public function setVendor(\Application\Entity\MlaVendors $vendor = null)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return \Application\Entity\MlaVendors
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return MlaDeliveryItems
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
     * Set lastWorkflow
     *
     * @param \Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow
     *
     * @return MlaDeliveryItems
     */
    public function setLastWorkflow(\Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow = null)
    {
        $this->lastWorkflow = $lastWorkflow;

        return $this;
    }

    /**
     * Get lastWorkflow
     *
     * @return \Application\Entity\MlaDeliveryItemsWorkflows
     */
    public function getLastWorkflow()
    {
        return $this->lastWorkflow;
    }
}
