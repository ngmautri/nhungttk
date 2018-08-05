<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryGiRow
 *
 * @ORM\Table(name="nmt_inventory_gi_row", indexes={@ORM\Index(name="nmt_inventory_gi_row_IDX1", columns={"created_by"}), @ORM\Index(name="nmt_inventory_gi_row_IDX2", columns={"wh_id"}), @ORM\Index(name="nmt_inventory_gi_row_IDX3", columns={"currency_id"}), @ORM\Index(name="nmt_inventory_gi_row_IDX4", columns={"last_change_by"}), @ORM\Index(name="nmt_inventory_gi_row_IDX5", columns={"item_id"}), @ORM\Index(name="nmt_inventory_gi_row_IDX6", columns={"is_active"}), @ORM\Index(name="nmt_inventory_gi_row_IDX7", columns={"inventory_gi_id"}), @ORM\Index(name="nmt_inventory_gi_row_INDX8", columns={"inventory_gr_id"}), @ORM\Index(name="nmt_inventory_gi_row_INDX9", columns={"inventory_transfer_id"}), @ORM\Index(name="nmt_inventory_gi_row_IDX10", columns={"item_serial_id"}), @ORM\Index(name="nmt_inventory_gi_row_INDX11", columns={"item_batch_id"})})
 * @ORM\Entity
 */
class NmtInventoryGiRow
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
     * @ORM\Column(name="issue_for", type="integer", nullable=true)
     */
    private $issueFor;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="change_by", type="integer", nullable=true)
     */
    private $changeBy;

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
     * @ORM\Column(name="stock_remarks", type="string", length=45, nullable=true)
     */
    private $stockRemarks;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\NmtInventoryItemBatch
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemBatch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_batch_id", referencedColumnName="id")
     * })
     */
    private $itemBatch;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    private $currency;

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
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     * @var \Application\Entity\NmtInventoryGi
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryGi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_gi_id", referencedColumnName="id")
     * })
     */
    private $inventoryGi;

    /**
     * @var \Application\Entity\NmtInventoryGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_gr_id", referencedColumnName="id")
     * })
     */
    private $inventoryGr;

    /**
     * @var \Application\Entity\NmtInventoryTransfer
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryTransfer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventory_transfer_id", referencedColumnName="id")
     * })
     */
    private $inventoryTransfer;

    /**
     * @var \Application\Entity\NmtInventoryItemSerial
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItemSerial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_serial_id", referencedColumnName="id")
     * })
     */
    private $itemSerial;



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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set issueFor
     *
     * @param integer $issueFor
     *
     * @return NmtInventoryGiRow
     */
    public function setIssueFor($issueFor)
    {
        $this->issueFor = $issueFor;

        return $this;
    }

    /**
     * Get issueFor
     *
     * @return integer
     */
    public function getIssueFor()
    {
        return $this->issueFor;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set changeBy
     *
     * @param integer $changeBy
     *
     * @return NmtInventoryGiRow
     */
    public function setChangeBy($changeBy)
    {
        $this->changeBy = $changeBy;

        return $this;
    }

    /**
     * Get changeBy
     *
     * @return integer
     */
    public function getChangeBy()
    {
        return $this->changeBy;
    }

    /**
     * Set revisionNumber
     *
     * @param integer $revisionNumber
     *
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set stockRemarks
     *
     * @param string $stockRemarks
     *
     * @return NmtInventoryGiRow
     */
    public function setStockRemarks($stockRemarks)
    {
        $this->stockRemarks = $stockRemarks;

        return $this;
    }

    /**
     * Get stockRemarks
     *
     * @return string
     */
    public function getStockRemarks()
    {
        return $this->stockRemarks;
    }

    /**
     * Set sourceClass
     *
     * @param string $sourceClass
     *
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set glAccountId
     *
     * @param integer $glAccountId
     *
     * @return NmtInventoryGiRow
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
     * @return NmtInventoryGiRow
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryGiRow
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
     * Set itemBatch
     *
     * @param \Application\Entity\NmtInventoryItemBatch $itemBatch
     *
     * @return NmtInventoryGiRow
     */
    public function setItemBatch(\Application\Entity\NmtInventoryItemBatch $itemBatch = null)
    {
        $this->itemBatch = $itemBatch;

        return $this;
    }

    /**
     * Get itemBatch
     *
     * @return \Application\Entity\NmtInventoryItemBatch
     */
    public function getItemBatch()
    {
        return $this->itemBatch;
    }

    /**
     * Set wh
     *
     * @param \Application\Entity\NmtInventoryWarehouse $wh
     *
     * @return NmtInventoryGiRow
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
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return NmtInventoryGiRow
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

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtInventoryGiRow
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryGiRow
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
     * Set inventoryGi
     *
     * @param \Application\Entity\NmtInventoryGi $inventoryGi
     *
     * @return NmtInventoryGiRow
     */
    public function setInventoryGi(\Application\Entity\NmtInventoryGi $inventoryGi = null)
    {
        $this->inventoryGi = $inventoryGi;

        return $this;
    }

    /**
     * Get inventoryGi
     *
     * @return \Application\Entity\NmtInventoryGi
     */
    public function getInventoryGi()
    {
        return $this->inventoryGi;
    }

    /**
     * Set inventoryGr
     *
     * @param \Application\Entity\NmtInventoryGr $inventoryGr
     *
     * @return NmtInventoryGiRow
     */
    public function setInventoryGr(\Application\Entity\NmtInventoryGr $inventoryGr = null)
    {
        $this->inventoryGr = $inventoryGr;

        return $this;
    }

    /**
     * Get inventoryGr
     *
     * @return \Application\Entity\NmtInventoryGr
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }

    /**
     * Set inventoryTransfer
     *
     * @param \Application\Entity\NmtInventoryTransfer $inventoryTransfer
     *
     * @return NmtInventoryGiRow
     */
    public function setInventoryTransfer(\Application\Entity\NmtInventoryTransfer $inventoryTransfer = null)
    {
        $this->inventoryTransfer = $inventoryTransfer;

        return $this;
    }

    /**
     * Get inventoryTransfer
     *
     * @return \Application\Entity\NmtInventoryTransfer
     */
    public function getInventoryTransfer()
    {
        return $this->inventoryTransfer;
    }

    /**
     * Set itemSerial
     *
     * @param \Application\Entity\NmtInventoryItemSerial $itemSerial
     *
     * @return NmtInventoryGiRow
     */
    public function setItemSerial(\Application\Entity\NmtInventoryItemSerial $itemSerial = null)
    {
        $this->itemSerial = $itemSerial;

        return $this;
    }

    /**
     * Get itemSerial
     *
     * @return \Application\Entity\NmtInventoryItemSerial
     */
    public function getItemSerial()
    {
        return $this->itemSerial;
    }
}
