<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPoItem
 *
 * @ORM\Table(name="mla_po_item", uniqueConstraints={@ORM\UniqueConstraint(name="pr_item_id_UNIQUE", columns={"pr_item_id"})}, indexes={@ORM\Index(name="mla_delivery_items_FK1_idx1", columns={"pr_item_id"}), @ORM\Index(name="mla_delivery_items_FK3_idx", columns={"vendor_id"}), @ORM\Index(name="mla_delivery_items_FK4_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class MlaPoItem
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
     * @ORM\Column(name="po_id", type="integer", nullable=true)
     */
    private $poId;

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
     * Set poId
     *
     * @param integer $poId
     *
     * @return MlaPoItem
     */
    public function setPoId($poId)
    {
        $this->poId = $poId;

        return $this;
    }

    /**
     * Get poId
     *
     * @return integer
     */
    public function getPoId()
    {
        return $this->poId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * Set price
     *
     * @param float $price
     *
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * Set status
     *
     * @param string $status
     *
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
     * @return MlaPoItem
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
