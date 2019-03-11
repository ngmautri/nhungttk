<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemBatch
 *
 * @ORM\Table(name="nmt_inventory_item_batch", indexes={@ORM\Index(name="nmt_inventory_item_batch_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_batch_FK2_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_inventory_item_batch_FK3_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_batch_FK4_idx", columns={"gr_row_id"}), @ORM\Index(name="nmt_inventory_item_batch_FK5_idx", columns={"ap_row_id"})})
 * @ORM\Entity
 */
class NmtInventoryItemBatch
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
     * @ORM\Column(name="batch_number", type="string", length=45, nullable=true)
     */
    private $batchNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * @var \DateTime
     *
     * @ORM\Column(name="consumed_on", type="datetime", nullable=true)
     */
    private $consumedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_serial_number", type="string", length=45, nullable=true)
     */
    private $mfgSerialNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_date", type="datetime", nullable=true)
     */
    private $mfgDate;

    /**
     * @var string
     *
     * @ORM\Column(name="lot_number", type="string", length=45, nullable=true)
     */
    private $lotNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_warranty_start", type="datetime", nullable=true)
     */
    private $mfgWarrantyStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mfg_warranty_end", type="datetime", nullable=true)
     */
    private $mfgWarrantyEnd;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name", type="string", length=100, nullable=true)
     */
    private $itemName;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=45, nullable=true)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=45, nullable=true)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_name", type="string", length=100, nullable=true)
     */
    private $mfgName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;

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
     * @var \Application\Entity\NmtProcureGrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_row_id", referencedColumnName="id")
     * })
     */
    private $grRow;

    /**
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_row_id", referencedColumnName="id")
     * })
     */
    private $apRow;



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
     * @return NmtInventoryItemBatch
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
     * Set batchNumber
     *
     * @param string $batchNumber
     *
     * @return NmtInventoryItemBatch
     */
    public function setBatchNumber($batchNumber)
    {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    /**
     * Get batchNumber
     *
     * @return string
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemBatch
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * Set consumedOn
     *
     * @param \DateTime $consumedOn
     *
     * @return NmtInventoryItemBatch
     */
    public function setConsumedOn($consumedOn)
    {
        $this->consumedOn = $consumedOn;

        return $this;
    }

    /**
     * Get consumedOn
     *
     * @return \DateTime
     */
    public function getConsumedOn()
    {
        return $this->consumedOn;
    }

    /**
     * Set mfgSerialNumber
     *
     * @param string $mfgSerialNumber
     *
     * @return NmtInventoryItemBatch
     */
    public function setMfgSerialNumber($mfgSerialNumber)
    {
        $this->mfgSerialNumber = $mfgSerialNumber;

        return $this;
    }

    /**
     * Get mfgSerialNumber
     *
     * @return string
     */
    public function getMfgSerialNumber()
    {
        return $this->mfgSerialNumber;
    }

    /**
     * Set mfgDate
     *
     * @param \DateTime $mfgDate
     *
     * @return NmtInventoryItemBatch
     */
    public function setMfgDate($mfgDate)
    {
        $this->mfgDate = $mfgDate;

        return $this;
    }

    /**
     * Get mfgDate
     *
     * @return \DateTime
     */
    public function getMfgDate()
    {
        return $this->mfgDate;
    }

    /**
     * Set lotNumber
     *
     * @param string $lotNumber
     *
     * @return NmtInventoryItemBatch
     */
    public function setLotNumber($lotNumber)
    {
        $this->lotNumber = $lotNumber;

        return $this;
    }

    /**
     * Get lotNumber
     *
     * @return string
     */
    public function getLotNumber()
    {
        return $this->lotNumber;
    }

    /**
     * Set mfgWarrantyStart
     *
     * @param \DateTime $mfgWarrantyStart
     *
     * @return NmtInventoryItemBatch
     */
    public function setMfgWarrantyStart($mfgWarrantyStart)
    {
        $this->mfgWarrantyStart = $mfgWarrantyStart;

        return $this;
    }

    /**
     * Get mfgWarrantyStart
     *
     * @return \DateTime
     */
    public function getMfgWarrantyStart()
    {
        return $this->mfgWarrantyStart;
    }

    /**
     * Set mfgWarrantyEnd
     *
     * @param \DateTime $mfgWarrantyEnd
     *
     * @return NmtInventoryItemBatch
     */
    public function setMfgWarrantyEnd($mfgWarrantyEnd)
    {
        $this->mfgWarrantyEnd = $mfgWarrantyEnd;

        return $this;
    }

    /**
     * Get mfgWarrantyEnd
     *
     * @return \DateTime
     */
    public function getMfgWarrantyEnd()
    {
        return $this->mfgWarrantyEnd;
    }

    /**
     * Set itemName
     *
     * @param string $itemName
     *
     * @return NmtInventoryItemBatch
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get itemName
     *
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set location
     *
     * @param string $location
     *
     * @return NmtInventoryItemBatch
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return NmtInventoryItemBatch
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set mfgName
     *
     * @param string $mfgName
     *
     * @return NmtInventoryItemBatch
     */
    public function setMfgName($mfgName)
    {
        $this->mfgName = $mfgName;

        return $this;
    }

    /**
     * Get mfgName
     *
     * @return string
     */
    public function getMfgName()
    {
        return $this->mfgName;
    }

    /**
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtInventoryItemBatch
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * @return NmtInventoryItemBatch
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
     * Set lastChangeBy
     *
     * @param integer $lastChangeBy
     *
     * @return NmtInventoryItemBatch
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryItemBatch
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
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemBatch
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
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return NmtInventoryItemBatch
     */
    public function setGrRow(\Application\Entity\NmtProcureGrRow $grRow = null)
    {
        $this->grRow = $grRow;

        return $this;
    }

    /**
     * Get grRow
     *
     * @return \Application\Entity\NmtProcureGrRow
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     * Set apRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apRow
     *
     * @return NmtInventoryItemBatch
     */
    public function setApRow(\Application\Entity\FinVendorInvoiceRow $apRow = null)
    {
        $this->apRow = $apRow;

        return $this;
    }

    /**
     * Get apRow
     *
     * @return \Application\Entity\FinVendorInvoiceRow
     */
    public function getApRow()
    {
        return $this->apRow;
    }
}
