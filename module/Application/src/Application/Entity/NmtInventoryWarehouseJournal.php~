<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWarehouseJournal
 *
 * @ORM\Table(name="nmt_inventory_warehouse_journal", indexes={@ORM\Index(name="nmt_inventory_warehouse_journal_idx", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_warehouse_journal_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_warehouse_journal_FK4_idx", columns={"currency"})})
 * @ORM\Entity
 */
class NmtInventoryWarehouseJournal
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
     * @ORM\Column(name="item_id", type="integer", nullable=false)
     */
    private $itemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_in", type="integer", nullable=true)
     */
    private $quantityIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_out", type="integer", nullable=true)
     */
    private $quantityOut;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $price;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency", referencedColumnName="id")
     * })
     */
    private $currency;



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
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return NmtInventoryWarehouseJournal
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryWarehouseJournal
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
     * Set quantityIn
     *
     * @param integer $quantityIn
     *
     * @return NmtInventoryWarehouseJournal
     */
    public function setQuantityIn($quantityIn)
    {
        $this->quantityIn = $quantityIn;

        return $this;
    }

    /**
     * Get quantityIn
     *
     * @return integer
     */
    public function getQuantityIn()
    {
        return $this->quantityIn;
    }

    /**
     * Set quantityOut
     *
     * @param integer $quantityOut
     *
     * @return NmtInventoryWarehouseJournal
     */
    public function setQuantityOut($quantityOut)
    {
        $this->quantityOut = $quantityOut;

        return $this;
    }

    /**
     * Get quantityOut
     *
     * @return integer
     */
    public function getQuantityOut()
    {
        return $this->quantityOut;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return NmtInventoryWarehouseJournal
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return NmtInventoryWarehouseJournal
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

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryWarehouseJournal
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
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return NmtInventoryWarehouseJournal
     */
    public function setCurrency(\Application\Entity\NmtApplicationCurrency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
