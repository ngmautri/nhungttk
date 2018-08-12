<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryMvRow
 *
 * @ORM\Table(name="nmt_inventory_mv_row", indexes={@ORM\Index(name="nmt_inventory_gi_row_IDX6", columns={"is_active"})})
 * @ORM\Entity
 */
class NmtInventoryMvRow
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
     * @var integer
     *
     * @ORM\Column(name="wh_id", type="integer", nullable=false)
     */
    private $whId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=false)
     */
    private $itemId;

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
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

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
     * @ORM\Column(name="currency_id", type="integer", nullable=true)
     */
    private $currencyId;

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
     * @var integer
     *
     * @ORM\Column(name="inventory_gr_id", type="integer", nullable=true)
     */
    private $inventoryGrId;

    /**
     * @var integer
     *
     * @ORM\Column(name="inventory_transfer_id", type="integer", nullable=true)
     */
    private $inventoryTransferId;

    /**
     * @var integer
     *
     * @ORM\Column(name="inventory_gi_id", type="integer", nullable=true)
     */
    private $inventoryGiId;

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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * Set whId
     *
     * @param integer $whId
     *
     * @return NmtInventoryMvRow
     */
    public function setWhId($whId)
    {
        $this->whId = $whId;

        return $this;
    }

    /**
     * Get whId
     *
     * @return integer
     */
    public function getWhId()
    {
        return $this->whId;
    }

    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return NmtInventoryMvRow
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
     * Set trxDate
     *
     * @param \DateTime $trxDate
     *
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtInventoryMvRow
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * Set currencyId
     *
     * @param integer $currencyId
     *
     * @return NmtInventoryMvRow
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * Get currencyId
     *
     * @return integer
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * Set inventoryGrId
     *
     * @param integer $inventoryGrId
     *
     * @return NmtInventoryMvRow
     */
    public function setInventoryGrId($inventoryGrId)
    {
        $this->inventoryGrId = $inventoryGrId;

        return $this;
    }

    /**
     * Get inventoryGrId
     *
     * @return integer
     */
    public function getInventoryGrId()
    {
        return $this->inventoryGrId;
    }

    /**
     * Set inventoryTransferId
     *
     * @param integer $inventoryTransferId
     *
     * @return NmtInventoryMvRow
     */
    public function setInventoryTransferId($inventoryTransferId)
    {
        $this->inventoryTransferId = $inventoryTransferId;

        return $this;
    }

    /**
     * Get inventoryTransferId
     *
     * @return integer
     */
    public function getInventoryTransferId()
    {
        return $this->inventoryTransferId;
    }

    /**
     * Set inventoryGiId
     *
     * @param integer $inventoryGiId
     *
     * @return NmtInventoryMvRow
     */
    public function setInventoryGiId($inventoryGiId)
    {
        $this->inventoryGiId = $inventoryGiId;

        return $this;
    }

    /**
     * Get inventoryGiId
     *
     * @return integer
     */
    public function getInventoryGiId()
    {
        return $this->inventoryGiId;
    }

    /**
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
     * @return NmtInventoryMvRow
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
}
