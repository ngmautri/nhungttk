<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseRequestItemsQuotation
 *
 * @ORM\Table(name="mla_purchase_request_items_quotation")
 * @ORM\Entity
 */
class MlaPurchaseRequestItemsQuotation
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
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="unit_price", type="float", precision=10, scale=0, nullable=true)
     */
    private $unitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_term", type="string", length=45, nullable=true)
     */
    private $deliveryTerm;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_term", type="string", length=45, nullable=true)
     */
    private $paymentTerm;

    /**
     * @var integer
     *
     * @ORM\Column(name="vendor_id", type="integer", nullable=true)
     */
    private $vendorId;



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
     * Set name
     *
     * @param string $name
     *
     * @return MlaPurchaseRequestItemsQuotation
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
     * Set description
     *
     * @param string $description
     *
     * @return MlaPurchaseRequestItemsQuotation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set unitPrice
     *
     * @param float $unitPrice
     *
     * @return MlaPurchaseRequestItemsQuotation
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Get unitPrice
     *
     * @return float
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * Set deliveryTerm
     *
     * @param string $deliveryTerm
     *
     * @return MlaPurchaseRequestItemsQuotation
     */
    public function setDeliveryTerm($deliveryTerm)
    {
        $this->deliveryTerm = $deliveryTerm;

        return $this;
    }

    /**
     * Get deliveryTerm
     *
     * @return string
     */
    public function getDeliveryTerm()
    {
        return $this->deliveryTerm;
    }

    /**
     * Set paymentTerm
     *
     * @param string $paymentTerm
     *
     * @return MlaPurchaseRequestItemsQuotation
     */
    public function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * Get paymentTerm
     *
     * @return string
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * Set vendorId
     *
     * @param integer $vendorId
     *
     * @return MlaPurchaseRequestItemsQuotation
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    /**
     * Get vendorId
     *
     * @return integer
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }
}
