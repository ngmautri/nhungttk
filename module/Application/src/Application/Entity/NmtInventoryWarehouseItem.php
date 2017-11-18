<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWarehouseItem
 *
 * @ORM\Table(name="nmt_inventory_warehouse_item", indexes={@ORM\Index(name="nmt_inventory_warehouse_item_KF1_idx", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_warehouse_item_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_warehouse_item_FK2_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class NmtInventoryWarehouseItem
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
     * @ORM\Column(name="on_hand", type="integer", nullable=true)
     */
    private $onHand;

    /**
     * @var integer
     *
     * @ORM\Column(name="on_order", type="integer", nullable=true)
     */
    private $onOrder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     *   @ORM\JoinColumn(name="wh_id", referencedColumnName="id")
     * })
     */
    private $wh;



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
     * Set onHand
     *
     * @param integer $onHand
     *
     * @return NmtInventoryWarehouseItem
     */
    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;

        return $this;
    }

    /**
     * Get onHand
     *
     * @return integer
     */
    public function getOnHand()
    {
        return $this->onHand;
    }

    /**
     * Set onOrder
     *
     * @param integer $onOrder
     *
     * @return NmtInventoryWarehouseItem
     */
    public function setOnOrder($onOrder)
    {
        $this->onOrder = $onOrder;

        return $this;
    }

    /**
     * Get onOrder
     *
     * @return integer
     */
    public function getOnOrder()
    {
        return $this->onOrder;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryWarehouseItem
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryWarehouseItem
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryWarehouseItem
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
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return NmtInventoryWarehouseItem
     */
    public function setWh(\Application\Entity\NmtInventoryWarehouse $wh = null)
    {
        $this->wh = $wh;

        return $this;
    }

    /**
     * Get wh
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWh()
    {
        return $this->wh;
    }
}
