<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryFifoLayer
 *
 * @ORM\Table(name="nmt_inventory_fifo_layer", indexes={@ORM\Index(name="nmt_inventory_fifo_layer_FK1_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK2_idx", columns={"doc_currency"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK3_idx", columns={"local_currency"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK4_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK4_idx1", columns={"warehouse_id"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK6_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_fifo_layer_FK7_idx", columns={"inventory_trx_id"})})
 * @ORM\Entity
 */
class NmtInventoryFifoLayer
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
     * @var boolean
     *
     * @ORM\Column(name="is_closed", type="boolean", nullable=true)
     */
    private $isClosed;

    /**
     * @var string
     *
     * @ORM\Column(name="open_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $openQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="open_value", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $openValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="closed_on", type="datetime", nullable=true)
     */
    private $closedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $sourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="source_class", type="string", length=255, nullable=true)
     */
    private $sourceClass;

    /**
     * @var string
     *
     * @ORM\Column(name="source_token", type="string", length=45, nullable=true)
     */
    private $sourceToken;

    /**
     * @var string
     *
     * @ORM\Column(name="onhand_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $onhandQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="local_unit_price", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $localUnitPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_open_balance", type="boolean", nullable=true)
     */
    private $isOpenBalance;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

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
     *   @ORM\JoinColumn(name="doc_currency", referencedColumnName="id")
     * })
     */
    private $docCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="local_currency", referencedColumnName="id")
     * })
     */
    private $localCurrency;

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
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     * })
     */
    private $warehouse;

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
     * @var \Application\Entity\NmtInventoryTrx
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryTrx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_trx_id", referencedColumnName="id")
     * })
     */
    private $inventoryTrx;



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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * Set isClosed
     *
     * @param boolean $isClosed
     *
     * @return NmtInventoryFifoLayer
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    /**
     * Get isClosed
     *
     * @return boolean
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Set openQuantity
     *
     * @param string $openQuantity
     *
     * @return NmtInventoryFifoLayer
     */
    public function setOpenQuantity($openQuantity)
    {
        $this->openQuantity = $openQuantity;

        return $this;
    }

    /**
     * Get openQuantity
     *
     * @return string
     */
    public function getOpenQuantity()
    {
        return $this->openQuantity;
    }

    /**
     * Set openValue
     *
     * @param string $openValue
     *
     * @return NmtInventoryFifoLayer
     */
    public function setOpenValue($openValue)
    {
        $this->openValue = $openValue;

        return $this;
    }

    /**
     * Get openValue
     *
     * @return string
     */
    public function getOpenValue()
    {
        return $this->openValue;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryFifoLayer
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
     * Set closedOn
     *
     * @param \DateTime $closedOn
     *
     * @return NmtInventoryFifoLayer
     */
    public function setClosedOn($closedOn)
    {
        $this->closedOn = $closedOn;

        return $this;
    }

    /**
     * Get closedOn
     *
     * @return \DateTime
     */
    public function getClosedOn()
    {
        return $this->closedOn;
    }

    /**
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return NmtInventoryFifoLayer
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId
     *
     * @return integer
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set sourceClass
     *
     * @param string $sourceClass
     *
     * @return NmtInventoryFifoLayer
     */
    public function setSourceClass($sourceClass)
    {
        $this->sourceClass = $sourceClass;

        return $this;
    }

    /**
     * Get sourceClass
     *
     * @return string
     */
    public function getSourceClass()
    {
        return $this->sourceClass;
    }

    /**
     * Set sourceToken
     *
     * @param string $sourceToken
     *
     * @return NmtInventoryFifoLayer
     */
    public function setSourceToken($sourceToken)
    {
        $this->sourceToken = $sourceToken;

        return $this;
    }

    /**
     * Get sourceToken
     *
     * @return string
     */
    public function getSourceToken()
    {
        return $this->sourceToken;
    }

    /**
     * Set onhandQuantity
     *
     * @param string $onhandQuantity
     *
     * @return NmtInventoryFifoLayer
     */
    public function setOnhandQuantity($onhandQuantity)
    {
        $this->onhandQuantity = $onhandQuantity;

        return $this;
    }

    /**
     * Get onhandQuantity
     *
     * @return string
     */
    public function getOnhandQuantity()
    {
        return $this->onhandQuantity;
    }

    /**
     * Set localUnitPrice
     *
     * @param string $localUnitPrice
     *
     * @return NmtInventoryFifoLayer
     */
    public function setLocalUnitPrice($localUnitPrice)
    {
        $this->localUnitPrice = $localUnitPrice;

        return $this;
    }

    /**
     * Get localUnitPrice
     *
     * @return string
     */
    public function getLocalUnitPrice()
    {
        return $this->localUnitPrice;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return NmtInventoryFifoLayer
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryFifoLayer
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
     * Set isOpenBalance
     *
     * @param boolean $isOpenBalance
     *
     * @return NmtInventoryFifoLayer
     */
    public function setIsOpenBalance($isOpenBalance)
    {
        $this->isOpenBalance = $isOpenBalance;

        return $this;
    }

    /**
     * Get isOpenBalance
     *
     * @return boolean
     */
    public function getIsOpenBalance()
    {
        return $this->isOpenBalance;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryFifoLayer
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryFifoLayer
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
     * @return NmtInventoryFifoLayer
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
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return NmtInventoryFifoLayer
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryFifoLayer
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
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtInventoryFifoLayer
     */
    public function setWarehouse(\Application\Entity\NmtInventoryWarehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryFifoLayer
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

    /**
     * Set inventoryTrx
     *
     * @param \Application\Entity\NmtInventoryTrx $inventoryTrx
     *
     * @return NmtInventoryFifoLayer
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
}
