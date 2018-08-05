<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryFifoLayerConsume
 *
 * @ORM\Table(name="nmt_inventory_fifo_layer_consume", indexes={@ORM\Index(name="nmt_inventory_fifo_layer_consume_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK2_idx", columns={"inventory_trx_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK3_idx", columns={"layer_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK4_idx", columns={"local_currency_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK5_idx", columns={"item_id"})})
 * @ORM\Entity
 */
class NmtInventoryFifoLayerConsume
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="total_value", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $totalValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $exchangeRate;

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
     * @var \Application\Entity\NmtInventoryTrx
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryTrx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_trx_id", referencedColumnName="id")
     * })
     */
    private $inventoryTrx;

    /**
     * @var \Application\Entity\NmtInventoryFifoLayer
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryFifoLayer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layer_id", referencedColumnName="id")
     * })
     */
    private $layer;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="local_currency_id", referencedColumnName="id")
     * })
     */
    private $localCurrency;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtInventoryFifoLayerConsume
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
     * @param string $quantity
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set docUnitPrice
     *
     * @param string $docUnitPrice
     *
     * @return NmtInventoryFifoLayerConsume
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
     * Set totalValue
     *
     * @param string $totalValue
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setTotalValue($totalValue)
    {
        $this->totalValue = $totalValue;

        return $this;
    }

    /**
     * Get totalValue
     *
     * @return string
     */
    public function getTotalValue()
    {
        return $this->totalValue;
    }

    /**
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setPostingDate($postingDate)
    {
        $this->postingDate = $postingDate;

        return $this;
    }

    /**
     * Get postingDate
     *
     * @return \DateTime
     */
    public function getPostingDate()
    {
        return $this->postingDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryFifoLayerConsume
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
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;

        return $this;
    }

    /**
     * Get exchangeRate
     *
     * @return string
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryFifoLayerConsume
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
     * Set inventoryTrx
     *
     * @param \Application\Entity\NmtInventoryTrx $inventoryTrx
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setInventoryTrx(\Application\Entity\NmtInventoryTrx $inventoryTrx = null)
    {
        $this->inventoryTrx = $inventoryTrx;

        return $this;
    }

    /**
     * Get inventoryTrx
     *
     * @return \Application\Entity\NmtInventoryTrx
     */
    public function getInventoryTrx()
    {
        return $this->inventoryTrx;
    }

    /**
     * Set layer
     *
     * @param \Application\Entity\NmtInventoryFifoLayer $layer
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setLayer(\Application\Entity\NmtInventoryFifoLayer $layer = null)
    {
        $this->layer = $layer;

        return $this;
    }

    /**
     * Get layer
     *
     * @return \Application\Entity\NmtInventoryFifoLayer
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setLocalCurrency(\Application\Entity\NmtApplicationCurrency $localCurrency = null)
    {
        $this->localCurrency = $localCurrency;

        return $this;
    }

    /**
     * Get localCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryFifoLayerConsume
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
}
