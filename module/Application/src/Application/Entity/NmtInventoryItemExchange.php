<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemExchange
 *
 * @ORM\Table(name="nmt_inventory_item_exchange", indexes={@ORM\Index(name="nmt_inventory_item_exchange_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_exchange_FK2_idx", columns={"trx_id"}), @ORM\Index(name="nmt_inventory_item_exchange_FK3_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_exchange_FK4_idx", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_item_exchange_FK5_idx", columns={"change_by"})})
 * @ORM\Entity
 */
class NmtInventoryItemExchange
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
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trx_date", type="datetime", nullable=true)
     */
    private $trxDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="trx_type_id", type="integer", nullable=true)
     */
    private $trxTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="flow", type="string", nullable=false)
     */
    private $flow;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

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
     * @ORM\Column(name="last_change_by", type="integer", nullable=true)
     */
    private $lastChangeBy;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $conversionFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_text", type="string", length=45, nullable=true)
     */
    private $conversionText;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_id", type="integer", nullable=true)
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="target_class", type="string", length=45, nullable=true)
     */
    private $targetClass;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=45, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="change_on", type="string", length=45, nullable=true)
     */
    private $changeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_number", type="integer", nullable=true)
     */
    private $revisionNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_posted", type="boolean", nullable=true)
     */
    private $isPosted;

    /**
     * @var integer
     *
     * @ORM\Column(name="actual_quantity", type="integer", nullable=true)
     */
    private $actualQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=45, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="source_class", type="string", length=255, nullable=true)
     */
    private $sourceClass;

    /**
     * @var integer
     *
     * @ORM\Column(name="source_id", type="integer", nullable=true)
     */
    private $sourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=45, nullable=true)
     */
    private $transactionType;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_serial_id", type="integer", nullable=true)
     */
    private $itemSerialId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_batch_id", type="integer", nullable=true)
     */
    private $itemBatchId;

    /**
     * @var integer
     *
     * @ORM\Column(name="GL_account_id", type="integer", nullable=true)
     */
    private $glAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="item_cost", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $itemCost;

    /**
     * @var integer
     *
     * @ORM\Column(name="movement_id", type="integer", nullable=true)
     */
    private $movementId;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_type", type="string", length=10, nullable=true)
     */
    private $movementType;

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
     *   @ORM\JoinColumn(name="trx_id", referencedColumnName="id")
     * })
     */
    private $trx;

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
     *   @ORM\JoinColumn(name="change_by", referencedColumnName="id")
     * })
     */
    private $changeBy;



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
     * @return NmtInventoryItemExchange
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
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtInventoryItemExchange
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set trxDate
     *
     * @param \DateTime $trxDate
     *
     * @return NmtInventoryItemExchange
     */
    public function setTrxDate($trxDate)
    {
        $this->trxDate = $trxDate;

        return $this;
    }

    /**
     * Get trxDate
     *
     * @return \DateTime
     */
    public function getTrxDate()
    {
        return $this->trxDate;
    }

    /**
     * Set trxTypeId
     *
     * @param integer $trxTypeId
     *
     * @return NmtInventoryItemExchange
     */
    public function setTrxTypeId($trxTypeId)
    {
        $this->trxTypeId = $trxTypeId;

        return $this;
    }

    /**
     * Get trxTypeId
     *
     * @return integer
     */
    public function getTrxTypeId()
    {
        return $this->trxTypeId;
    }

    /**
     * Set flow
     *
     * @param string $flow
     *
     * @return NmtInventoryItemExchange
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;

        return $this;
    }

    /**
     * Get flow
     *
     * @return string
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return NmtInventoryItemExchange
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemExchange
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryItemExchange
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
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return NmtInventoryItemExchange
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtInventoryItemExchange
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemExchange
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
     * @return NmtInventoryItemExchange
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
     * Set lastChangeBy
     *
     * @param integer $lastChangeBy
     *
     * @return NmtInventoryItemExchange
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return integer
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtInventoryItemExchange
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;

        return $this;
    }

    /**
     * Get conversionFactor
     *
     * @return string
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * Set conversionText
     *
     * @param string $conversionText
     *
     * @return NmtInventoryItemExchange
     */
    public function setConversionText($conversionText)
    {
        $this->conversionText = $conversionText;

        return $this;
    }

    /**
     * Get conversionText
     *
     * @return string
     */
    public function getConversionText()
    {
        return $this->conversionText;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return NmtInventoryItemExchange
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return integer
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set targetClass
     *
     * @param string $targetClass
     *
     * @return NmtInventoryItemExchange
     */
    public function setTargetClass($targetClass)
    {
        $this->targetClass = $targetClass;

        return $this;
    }

    /**
     * Get targetClass
     *
     * @return string
     */
    public function getTargetClass()
    {
        return $this->targetClass;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtInventoryItemExchange
     */
    public function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;

        return $this;
    }

    /**
     * Get docStatus
     *
     * @return string
     */
    public function getDocStatus()
    {
        return $this->docStatus;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryItemExchange
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;

        return $this;
    }

    /**
     * Get sysNumber
     *
     * @return string
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     * Set changeOn
     *
     * @param string $changeOn
     *
     * @return NmtInventoryItemExchange
     */
    public function setChangeOn($changeOn)
    {
        $this->changeOn = $changeOn;

        return $this;
    }

    /**
     * Get changeOn
     *
     * @return string
     */
    public function getChangeOn()
    {
        return $this->changeOn;
    }

    /**
     * Set revisionNumber
     *
     * @param integer $revisionNumber
     *
     * @return NmtInventoryItemExchange
     */
    public function setRevisionNumber($revisionNumber)
    {
        $this->revisionNumber = $revisionNumber;

        return $this;
    }

    /**
     * Get revisionNumber
     *
     * @return integer
     */
    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    /**
     * Set isPosted
     *
     * @param boolean $isPosted
     *
     * @return NmtInventoryItemExchange
     */
    public function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;

        return $this;
    }

    /**
     * Get isPosted
     *
     * @return boolean
     */
    public function getIsPosted()
    {
        return $this->isPosted;
    }

    /**
     * Set actualQuantity
     *
     * @param integer $actualQuantity
     *
     * @return NmtInventoryItemExchange
     */
    public function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity = $actualQuantity;

        return $this;
    }

    /**
     * Get actualQuantity
     *
     * @return integer
     */
    public function getActualQuantity()
    {
        return $this->actualQuantity;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return NmtInventoryItemExchange
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * Get transactionStatus
     *
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Set sourceClass
     *
     * @param string $sourceClass
     *
     * @return NmtInventoryItemExchange
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return NmtInventoryItemExchange
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
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return NmtInventoryItemExchange
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * Get transactionType
     *
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set itemSerialId
     *
     * @param integer $itemSerialId
     *
     * @return NmtInventoryItemExchange
     */
    public function setItemSerialId($itemSerialId)
    {
        $this->itemSerialId = $itemSerialId;

        return $this;
    }

    /**
     * Get itemSerialId
     *
     * @return integer
     */
    public function getItemSerialId()
    {
        return $this->itemSerialId;
    }

    /**
     * Set itemBatchId
     *
     * @param integer $itemBatchId
     *
     * @return NmtInventoryItemExchange
     */
    public function setItemBatchId($itemBatchId)
    {
        $this->itemBatchId = $itemBatchId;

        return $this;
    }

    /**
     * Get itemBatchId
     *
     * @return integer
     */
    public function getItemBatchId()
    {
        return $this->itemBatchId;
    }

    /**
     * Set glAccountId
     *
     * @param integer $glAccountId
     *
     * @return NmtInventoryItemExchange
     */
    public function setGlAccountId($glAccountId)
    {
        $this->glAccountId = $glAccountId;

        return $this;
    }

    /**
     * Get glAccountId
     *
     * @return integer
     */
    public function getGlAccountId()
    {
        return $this->glAccountId;
    }

    /**
     * Set itemCost
     *
     * @param string $itemCost
     *
     * @return NmtInventoryItemExchange
     */
    public function setItemCost($itemCost)
    {
        $this->itemCost = $itemCost;

        return $this;
    }

    /**
     * Get itemCost
     *
     * @return string
     */
    public function getItemCost()
    {
        return $this->itemCost;
    }

    /**
     * Set movementId
     *
     * @param integer $movementId
     *
     * @return NmtInventoryItemExchange
     */
    public function setMovementId($movementId)
    {
        $this->movementId = $movementId;

        return $this;
    }

    /**
     * Get movementId
     *
     * @return integer
     */
    public function getMovementId()
    {
        return $this->movementId;
    }

    /**
     * Set movementType
     *
     * @param string $movementType
     *
     * @return NmtInventoryItemExchange
     */
    public function setMovementType($movementType)
    {
        $this->movementType = $movementType;

        return $this;
    }

    /**
     * Get movementType
     *
     * @return string
     */
    public function getMovementType()
    {
        return $this->movementType;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemExchange
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
     * Set trx
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     *
     * @return NmtInventoryItemExchange
     */
    public function setTrx(\Application\Entity\NmtInventoryTrx $trx = null)
    {
        $this->trx = $trx;

        return $this;
    }

    /**
     * Get trx
     *
     * @return \Application\Entity\NmtInventoryTrx
     */
    public function getTrx()
    {
        return $this->trx;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemExchange
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
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return NmtInventoryItemExchange
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
     * Set changeBy
     *
     * @param \Application\Entity\MlaUsers $changeBy
     *
     * @return NmtInventoryItemExchange
     */
    public function setChangeBy(\Application\Entity\MlaUsers $changeBy = null)
    {
        $this->changeBy = $changeBy;

        return $this;
    }

    /**
     * Get changeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }
}
