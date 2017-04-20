<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaDeliveryItemsWorkflows
 *
 * @ORM\Table(name="mla_delivery_items_workflows", indexes={@ORM\Index(name="mla_delivery_items_workflows_FK1_idx", columns={"delivery_id"}), @ORM\Index(name="mla_delivery_items_workflows_FK2_idx", columns={"dn_item_id"}), @ORM\Index(name="mla_delivery_items_workflows_FK3_idx", columns={"pr_item_id"}), @ORM\Index(name="mla_delivery_items_workflows_idx1", columns={"status"})})
 * @ORM\Entity
 */
class MlaDeliveryItemsWorkflows
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
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated_by", type="integer", nullable=true)
     */
    private $updatedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="confirmed_quantity", type="integer", nullable=true)
     */
    private $confirmedQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="rejected_quantity", type="integer", nullable=true)
     */
    private $rejectedQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

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
     * @var \Application\Entity\MlaDeliveryItems
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDeliveryItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dn_item_id", referencedColumnName="id")
     * })
     */
    private $dnItem;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return MlaDeliveryItemsWorkflows
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
     * Set updatedBy
     *
     * @param integer $updatedBy
     *
     * @return MlaDeliveryItemsWorkflows
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return integer
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return MlaDeliveryItemsWorkflows
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set confirmedQuantity
     *
     * @param integer $confirmedQuantity
     *
     * @return MlaDeliveryItemsWorkflows
     */
    public function setConfirmedQuantity($confirmedQuantity)
    {
        $this->confirmedQuantity = $confirmedQuantity;

        return $this;
    }

    /**
     * Get confirmedQuantity
     *
     * @return integer
     */
    public function getConfirmedQuantity()
    {
        return $this->confirmedQuantity;
    }

    /**
     * Set rejectedQuantity
     *
     * @param integer $rejectedQuantity
     *
     * @return MlaDeliveryItemsWorkflows
     */
    public function setRejectedQuantity($rejectedQuantity)
    {
        $this->rejectedQuantity = $rejectedQuantity;

        return $this;
    }

    /**
     * Get rejectedQuantity
     *
     * @return integer
     */
    public function getRejectedQuantity()
    {
        return $this->rejectedQuantity;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return MlaDeliveryItemsWorkflows
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
     * Set delivery
     *
     * @param \Application\Entity\MlaDelivery $delivery
     *
     * @return MlaDeliveryItemsWorkflows
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
     * Set dnItem
     *
     * @param \Application\Entity\MlaDeliveryItems $dnItem
     *
     * @return MlaDeliveryItemsWorkflows
     */
    public function setDnItem(\Application\Entity\MlaDeliveryItems $dnItem = null)
    {
        $this->dnItem = $dnItem;

        return $this;
    }

    /**
     * Get dnItem
     *
     * @return \Application\Entity\MlaDeliveryItems
     */
    public function getDnItem()
    {
        return $this->dnItem;
    }

    /**
     * Set prItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $prItem
     *
     * @return MlaDeliveryItemsWorkflows
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
}
