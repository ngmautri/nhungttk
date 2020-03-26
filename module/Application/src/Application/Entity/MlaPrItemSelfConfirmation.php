<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPrItemSelfConfirmation
 *
 * @ORM\Table(name="mla_pr_item_self_confirmation", indexes={@ORM\Index(name="mla_delivery_items_workflows_FK3_idx", columns={"pr_item_id"}), @ORM\Index(name="mla_delivery_items_workflows_idx1", columns={"status"}), @ORM\Index(name="mla_pr_item_self_confirmation_FK2_idx", columns={"updated_by"})})
 * @ORM\Entity
 */
class MlaPrItemSelfConfirmation
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
     * @var \Application\Entity\MlaPurchaseRequestItems
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaPurchaseRequestItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_item_id", referencedColumnName="id")
     * })
     */
    private $prItem;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;



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
     * @return MlaPrItemSelfConfirmation
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return MlaPrItemSelfConfirmation
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
     * @return MlaPrItemSelfConfirmation
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
     * @return MlaPrItemSelfConfirmation
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
     * @return MlaPrItemSelfConfirmation
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
     * Set prItem
     *
     * @param \Application\Entity\MlaPurchaseRequestItems $prItem
     *
     * @return MlaPrItemSelfConfirmation
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
     * Set updatedBy
     *
     * @param \Application\Entity\MlaUsers $updatedBy
     *
     * @return MlaPrItemSelfConfirmation
     */
    public function setUpdatedBy(\Application\Entity\MlaUsers $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
