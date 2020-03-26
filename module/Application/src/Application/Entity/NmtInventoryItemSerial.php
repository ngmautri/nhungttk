<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryItemSerial
 *
 * @ORM\Table(name="nmt_inventory_item_serial", indexes={@ORM\Index(name="nmt_inventory_item_serial_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_item_serial_FK2_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_inventory_item_serial_FK3_idx", columns={"item_id"}), @ORM\Index(name="nmt_inventory_item_serial_FK4_idx", columns={"serial_id"}), @ORM\Index(name="nmt_inventory_item_serial_FK5_idx", columns={"inventory_trx_id"}), @ORM\Index(name="nmt_inventory_item_serial_FK6_idx", columns={"ap_row_id"}), @ORM\Index(name="nmt_inventory_item_serial_FK7_idx", columns={"gr_row_id"}), @ORM\Index(name="nmt_inventory_item_serial_FK8_idx", columns={"origin_country"})})
 * @ORM\Entity
 */
class NmtInventoryItemSerial
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
     * @ORM\Column(name="serial_number", type="string", length=45, nullable=true)
     */
    private $serialNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number_1", type="string", length=45, nullable=true)
     */
    private $serialNumber1;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number_2", type="string", length=45, nullable=true)
     */
    private $serialNumber2;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number_3", type="string", length=45, nullable=true)
     */
    private $serialNumber3;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_model", type="string", length=45, nullable=true)
     */
    private $mfgModel;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_model1", type="string", length=45, nullable=true)
     */
    private $mfgModel1;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_model2", type="string", length=45, nullable=true)
     */
    private $mfgModel2;

    /**
     * @var string
     *
     * @ORM\Column(name="mfg_description", type="string", length=255, nullable=true)
     */
    private $mfgDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="capacity", type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @var string
     *
     * @ORM\Column(name="erp_asset_number", type="string", length=45, nullable=true)
     */
    private $erpAssetNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="erp_asset_number1", type="string", length=45, nullable=true)
     */
    private $erpAssetNumber1;

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
     * @var \Application\Entity\NmtInventorySerial
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventorySerial")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial_id", referencedColumnName="id")
     * })
     */
    private $serial;

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
     * @var \Application\Entity\FinVendorInvoiceRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoiceRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_row_id", referencedColumnName="id")
     * })
     */
    private $apRow;

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
     * @var \Application\Entity\NmtApplicationCountry
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCountry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origin_country", referencedColumnName="id")
     * })
     */
    private $originCountry;



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
     * @return NmtInventoryItemSerial
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
     * Set serialNumber
     *
     * @param string $serialNumber
     *
     * @return NmtInventoryItemSerial
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryItemSerial
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
     * Set serialNumber1
     *
     * @param string $serialNumber1
     *
     * @return NmtInventoryItemSerial
     */
    public function setSerialNumber1($serialNumber1)
    {
        $this->serialNumber1 = $serialNumber1;

        return $this;
    }

    /**
     * Get serialNumber1
     *
     * @return string
     */
    public function getSerialNumber1()
    {
        return $this->serialNumber1;
    }

    /**
     * Set serialNumber2
     *
     * @param string $serialNumber2
     *
     * @return NmtInventoryItemSerial
     */
    public function setSerialNumber2($serialNumber2)
    {
        $this->serialNumber2 = $serialNumber2;

        return $this;
    }

    /**
     * Get serialNumber2
     *
     * @return string
     */
    public function getSerialNumber2()
    {
        return $this->serialNumber2;
    }

    /**
     * Set serialNumber3
     *
     * @param string $serialNumber3
     *
     * @return NmtInventoryItemSerial
     */
    public function setSerialNumber3($serialNumber3)
    {
        $this->serialNumber3 = $serialNumber3;

        return $this;
    }

    /**
     * Get serialNumber3
     *
     * @return string
     */
    public function getSerialNumber3()
    {
        return $this->serialNumber3;
    }

    /**
     * Set mfgModel
     *
     * @param string $mfgModel
     *
     * @return NmtInventoryItemSerial
     */
    public function setMfgModel($mfgModel)
    {
        $this->mfgModel = $mfgModel;

        return $this;
    }

    /**
     * Get mfgModel
     *
     * @return string
     */
    public function getMfgModel()
    {
        return $this->mfgModel;
    }

    /**
     * Set mfgModel1
     *
     * @param string $mfgModel1
     *
     * @return NmtInventoryItemSerial
     */
    public function setMfgModel1($mfgModel1)
    {
        $this->mfgModel1 = $mfgModel1;

        return $this;
    }

    /**
     * Get mfgModel1
     *
     * @return string
     */
    public function getMfgModel1()
    {
        return $this->mfgModel1;
    }

    /**
     * Set mfgModel2
     *
     * @param string $mfgModel2
     *
     * @return NmtInventoryItemSerial
     */
    public function setMfgModel2($mfgModel2)
    {
        $this->mfgModel2 = $mfgModel2;

        return $this;
    }

    /**
     * Get mfgModel2
     *
     * @return string
     */
    public function getMfgModel2()
    {
        return $this->mfgModel2;
    }

    /**
     * Set mfgDescription
     *
     * @param string $mfgDescription
     *
     * @return NmtInventoryItemSerial
     */
    public function setMfgDescription($mfgDescription)
    {
        $this->mfgDescription = $mfgDescription;

        return $this;
    }

    /**
     * Get mfgDescription
     *
     * @return string
     */
    public function getMfgDescription()
    {
        return $this->mfgDescription;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     *
     * @return NmtInventoryItemSerial
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set erpAssetNumber
     *
     * @param string $erpAssetNumber
     *
     * @return NmtInventoryItemSerial
     */
    public function setErpAssetNumber($erpAssetNumber)
    {
        $this->erpAssetNumber = $erpAssetNumber;

        return $this;
    }

    /**
     * Get erpAssetNumber
     *
     * @return string
     */
    public function getErpAssetNumber()
    {
        return $this->erpAssetNumber;
    }

    /**
     * Set erpAssetNumber1
     *
     * @param string $erpAssetNumber1
     *
     * @return NmtInventoryItemSerial
     */
    public function setErpAssetNumber1($erpAssetNumber1)
    {
        $this->erpAssetNumber1 = $erpAssetNumber1;

        return $this;
    }

    /**
     * Get erpAssetNumber1
     *
     * @return string
     */
    public function getErpAssetNumber1()
    {
        return $this->erpAssetNumber1;
    }

    /**
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * @return NmtInventoryItemSerial
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
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return NmtInventoryItemSerial
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtInventoryItemSerial
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
     * Set serial
     *
     * @param \Application\Entity\NmtInventorySerial $serial
     *
     * @return NmtInventoryItemSerial
     */
    public function setSerial(\Application\Entity\NmtInventorySerial $serial = null)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return \Application\Entity\NmtInventorySerial
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Set inventoryTrx
     *
     * @param \Application\Entity\NmtInventoryTrx $inventoryTrx
     *
     * @return NmtInventoryItemSerial
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
     * Set apRow
     *
     * @param \Application\Entity\FinVendorInvoiceRow $apRow
     *
     * @return NmtInventoryItemSerial
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

    /**
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return NmtInventoryItemSerial
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
     * Set originCountry
     *
     * @param \Application\Entity\NmtApplicationCountry $originCountry
     *
     * @return NmtInventoryItemSerial
     */
    public function setOriginCountry(\Application\Entity\NmtApplicationCountry $originCountry = null)
    {
        $this->originCountry = $originCountry;

        return $this;
    }

    /**
     * Get originCountry
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getOriginCountry()
    {
        return $this->originCountry;
    }
}
