<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryFifoLayerConsume
 *
 * @ORM\Table(name="nmt_inventory_fifo_layer_consume", indexes={@ORM\Index(name="nmt_inventory_fifo_layer_consume_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK2_idx", columns={"inventory_trx_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK3_idx", columns={"layer_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK4_idx", columns={"local_currency_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK5_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK6_idx", columns={"doc_currency_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK7_idx", columns={"posting_period_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_consume_FK8_idx", columns={"last_change_by"})})
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
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=4, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="doc_total_value", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docTotalValue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversed", type="boolean", nullable=true)
     */
    private $isReversed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reversal_date", type="datetime", nullable=true)
     */
    private $reversalDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="reversal_doc", type="integer", nullable=true)
     */
    private $reversalDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="reversal_reason", type="string", length=100, nullable=true)
     */
    private $reversalReason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reversable", type="boolean", nullable=true)
     */
    private $isReversable;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_currency_id", referencedColumnName="id")
     * })
     */
    private $docCurrency;

    /**
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posting_period_id", referencedColumnName="id")
     * })
     */
    private $postingPeriod;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;



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
     * @param float $quantity
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
     * @return float
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
     * Set docTotalValue
     *
     * @param string $docTotalValue
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setDocTotalValue($docTotalValue)
    {
        $this->docTotalValue = $docTotalValue;

        return $this;
    }

    /**
     * Get docTotalValue
     *
     * @return string
     */
    public function getDocTotalValue()
    {
        return $this->docTotalValue;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;

        return $this;
    }

    /**
     * Get isReversed
     *
     * @return boolean
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * Set reversalDate
     *
     * @param \DateTime $reversalDate
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;

        return $this;
    }

    /**
     * Get reversalDate
     *
     * @return \DateTime
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * Set reversalDoc
     *
     * @param integer $reversalDoc
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;

        return $this;
    }

    /**
     * Get reversalDoc
     *
     * @return integer
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     * Set reversalReason
     *
     * @param string $reversalReason
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;

        return $this;
    }

    /**
     * Get reversalReason
     *
     * @return string
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * Set isReversable
     *
     * @param boolean $isReversable
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;

        return $this;
    }

    /**
     * Get isReversable
     *
     * @return boolean
     */
    public function getIsReversable()
    {
        return $this->isReversable;
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

    /**
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setDocCurrency(\Application\Entity\NmtApplicationCurrency $docCurrency = null)
    {
        $this->docCurrency = $docCurrency;

        return $this;
    }

    /**
     * Get docCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    /**
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setPostingPeriod(\Application\Entity\NmtFinPostingPeriod $postingPeriod = null)
    {
        $this->postingPeriod = $postingPeriod;

        return $this;
    }

    /**
     * Get postingPeriod
     *
     * @return \Application\Entity\NmtFinPostingPeriod
     */
    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryFifoLayerConsume
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }
}
