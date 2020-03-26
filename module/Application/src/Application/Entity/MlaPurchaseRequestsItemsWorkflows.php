<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseRequestsItemsWorkflows
 *
 * @ORM\Table(name="mla_purchase_requests_items_workflows", indexes={@ORM\Index(name="mla_purchase_requests_items_workflows_FK1_idx", columns={"pr_item_id"}), @ORM\Index(name="mla_purchase_requests_items_workflows_FK1_idx1", columns={"delivery_id"})})
 * @ORM\Entity
 */
class MlaPurchaseRequestsItemsWorkflows
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
     * @return MlaPurchaseRequestsItemsWorkflows
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
     * @return MlaPurchaseRequestsItemsWorkflows
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
     * @return MlaPurchaseRequestsItemsWorkflows
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
     * Set prItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $prItem
     *
     * @return MlaPurchaseRequestsItemsWorkflows
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
     * @return MlaPurchaseRequestsItemsWorkflows
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
}
