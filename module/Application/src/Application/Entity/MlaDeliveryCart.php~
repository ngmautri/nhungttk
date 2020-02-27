<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaDeliveryCart
 *
 * @ORM\Table(name="mla_delivery_cart", indexes={@ORM\Index(name="mla_delivery_items_FK1_idx1", columns={"pr_item_id"}), @ORM\Index(name="mla_delivery_items_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="mla_delivery_items_FK4_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class MlaDeliveryCart
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
     * @var integer
     *
     * @ORM\Column(name="delivery_id", type="integer", nullable=true)
     */
    private $deliveryId;

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
     * @var integer
     *
     * @ORM\Column(name="last_workflow_id", type="integer", nullable=true)
     */
    private $lastWorkflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

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
     * @return MlaDeliveryCart
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
     * Set deliveryId
     *
     * @param integer $deliveryId
     *
     * @return MlaDeliveryCart
     */
    public function setDeliveryId($deliveryId)
    {
        $this->deliveryId = $deliveryId;

        return $this;
    }

    /**
     * Get deliveryId
     *
     * @return integer
     */
    public function getDeliveryId()
    {
        return $this->deliveryId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
     * Set lastWorkflowId
     *
     * @param integer $lastWorkflowId
     *
     * @return MlaDeliveryCart
     */
    public function setLastWorkflowId($lastWorkflowId)
    {
        $this->lastWorkflowId = $lastWorkflowId;

        return $this;
    }

    /**
     * Get lastWorkflowId
     *
     * @return integer
     */
    public function getLastWorkflowId()
    {
        return $this->lastWorkflowId;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return MlaDeliveryCart
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
     * Set prItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $prItem
     *
     * @return MlaDeliveryCart
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
     * Set vendor
     *
     * @param \Application\Entity\MlaVendors $vendor
     *
     * @return MlaDeliveryCart
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
     * @return MlaDeliveryCart
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
