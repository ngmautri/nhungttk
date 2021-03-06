<?php
namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePrRow
 *
 * @ORM\Table(name="nmt_procure_pr_row", indexes={@ORM\Index(name="nmt_procure_pr_row_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_pr_row_FK2_idx", columns={"pr_id"}), @ORM\Index(name="nmt_procure_pr_row_FK4_idx", columns={"project_id"}), @ORM\Index(name="nmt_procure_pr_row_FK5_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_procure_pr_row_FK3_idx", columns={"item_id"}), @ORM\Index(name="nmt_procure_pr_row_IDX1", columns={"is_active"}), @ORM\Index(name="nmt_procure_pr_row_IDX2", columns={"current_state"}), @ORM\Index(name="nmt_procure_pr_row_FK6_idx", columns={"doc_uom"}), @ORM\Index(name="nmt_procure_pr_row_FK7_idx", columns={"warehouse_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\Repository\NmtProcurePrRowRepository")
 */
class NmtProcurePrRow
{

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="row_number", type="integer", nullable=true)
     */
    private $rowNumber;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="row_identifer", type="string", length=45, nullable=true)
     */
    private $rowIdentifer;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=45, nullable=true)
     */
    private $priority;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="row_name", type="string", length=60, nullable=true)
     */
    private $rowName;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="row_description", type="string", length=255, nullable=true)
     */
    private $rowDescription;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="row_code", type="string", length=100, nullable=true)
     */
    private $rowCode;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="row_unit", type="string", length=45, nullable=true)
     */
    private $rowUnit;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $conversionFactor;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="conversion_text", type="string", length=100, nullable=true)
     */
    private $conversionText;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", precision=10, scale=0, nullable=false)
     */
    private $quantity;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="edt", type="datetime", nullable=true)
     */
    private $edt;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="fa_remarks", type="string", length=100, nullable=true)
     */
    private $faRemarks;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="converted_stock_quantity", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStockQuantity;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="converted_standard_quantiy", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $convertedStandardQuantiy;

    /**
     *
     * @var float
     *
     * @ORM\Column(name="doc_quantity", type="float", precision=10, scale=0, nullable=true)
     */
    private $docQuantity;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="doc_unit", type="string", length=45, nullable=true)
     */
    private $docUnit;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=45, nullable=true)
     */
    private $docType;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="doc_version", type="integer", nullable=true)
     */
    private $docVersion;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=38, nullable=true)
     */
    private $uuid;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="standard_convert_factor", type="integer", nullable=true)
     */
    private $standardConvertFactor;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="vendor_item_name", type="string", length=100, nullable=true)
     */
    private $vendorItemName;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="vendor_item_code", type="string", length=45, nullable=true)
     */
    private $vendorItemCode;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="clearing_doc_id", type="integer", nullable=true)
     */
    private $clearingDocId;

    /**
     *
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     *
     * @var \Application\Entity\NmtProcurePr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     * })
     */
    private $pr;

    /**
     *
     * @var \Application\Entity\NmtInventoryItem
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     * })
     */
    private $item;

    /**
     *
     * @var \Application\Entity\NmtPmProject
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtPmProject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     *
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;

    /**
     *
     * @var \Application\Entity\NmtApplicationUom
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationUom")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_uom", referencedColumnName="id")
     * })
     */
    private $docUom;

    /**
     *
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     * })
     */
    private $warehouse;

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
     * Set rowNumber
     *
     * @param integer $rowNumber
     *
     * @return NmtProcurePrRow
     */
    public function setRowNumber($rowNumber)
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    /**
     * Get rowNumber
     *
     * @return integer
     */
    public function getRowNumber()
    {
        return $this->rowNumber;
    }

    /**
     * Set rowIdentifer
     *
     * @param string $rowIdentifer
     *
     * @return NmtProcurePrRow
     */
    public function setRowIdentifer($rowIdentifer)
    {
        $this->rowIdentifer = $rowIdentifer;

        return $this;
    }

    /**
     * Get rowIdentifer
     *
     * @return string
     */
    public function getRowIdentifer()
    {
        return $this->rowIdentifer;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtProcurePrRow
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
     * @return NmtProcurePrRow
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
     * Set priority
     *
     * @param string $priority
     *
     * @return NmtProcurePrRow
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set rowName
     *
     * @param string $rowName
     *
     * @return NmtProcurePrRow
     */
    public function setRowName($rowName)
    {
        $this->rowName = $rowName;

        return $this;
    }

    /**
     * Get rowName
     *
     * @return string
     */
    public function getRowName()
    {
        return $this->rowName;
    }

    /**
     * Set rowDescription
     *
     * @param string $rowDescription
     *
     * @return NmtProcurePrRow
     */
    public function setRowDescription($rowDescription)
    {
        $this->rowDescription = $rowDescription;

        return $this;
    }

    /**
     * Get rowDescription
     *
     * @return string
     */
    public function getRowDescription()
    {
        return $this->rowDescription;
    }

    /**
     * Set rowCode
     *
     * @param string $rowCode
     *
     * @return NmtProcurePrRow
     */
    public function setRowCode($rowCode)
    {
        $this->rowCode = $rowCode;

        return $this;
    }

    /**
     * Get rowCode
     *
     * @return string
     */
    public function getRowCode()
    {
        return $this->rowCode;
    }

    /**
     * Set rowUnit
     *
     * @param string $rowUnit
     *
     * @return NmtProcurePrRow
     */
    public function setRowUnit($rowUnit)
    {
        $this->rowUnit = $rowUnit;

        return $this;
    }

    /**
     * Get rowUnit
     *
     * @return string
     */
    public function getRowUnit()
    {
        return $this->rowUnit;
    }

    /**
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtProcurePrRow
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
     * @return NmtProcurePrRow
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
     * Set quantity
     *
     * @param float $quantity
     *
     * @return NmtProcurePrRow
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
     * Set edt
     *
     * @param \DateTime $edt
     *
     * @return NmtProcurePrRow
     */
    public function setEdt($edt)
    {
        $this->edt = $edt;

        return $this;
    }

    /**
     * Get edt
     *
     * @return \DateTime
     */
    public function getEdt()
    {
        return $this->edt;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtProcurePrRow
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
     * @return NmtProcurePrRow
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtProcurePrRow
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtProcurePrRow
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtProcurePrRow
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtProcurePrRow
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set faRemarks
     *
     * @param string $faRemarks
     *
     * @return NmtProcurePrRow
     */
    public function setFaRemarks($faRemarks)
    {
        $this->faRemarks = $faRemarks;

        return $this;
    }

    /**
     * Get faRemarks
     *
     * @return string
     */
    public function getFaRemarks()
    {
        return $this->faRemarks;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtProcurePrRow
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
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtProcurePrRow
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
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return NmtProcurePrRow
     */
    public function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;

        return $this;
    }

    /**
     * Get workflowStatus
     *
     * @return string
     */
    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return NmtProcurePrRow
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
     * Set convertedStockQuantity
     *
     * @param string $convertedStockQuantity
     *
     * @return NmtProcurePrRow
     */
    public function setConvertedStockQuantity($convertedStockQuantity)
    {
        $this->convertedStockQuantity = $convertedStockQuantity;

        return $this;
    }

    /**
     * Get convertedStockQuantity
     *
     * @return string
     */
    public function getConvertedStockQuantity()
    {
        return $this->convertedStockQuantity;
    }

    /**
     * Set convertedStandardQuantiy
     *
     * @param string $convertedStandardQuantiy
     *
     * @return NmtProcurePrRow
     */
    public function setConvertedStandardQuantiy($convertedStandardQuantiy)
    {
        $this->convertedStandardQuantiy = $convertedStandardQuantiy;

        return $this;
    }

    /**
     * Get convertedStandardQuantiy
     *
     * @return string
     */
    public function getConvertedStandardQuantiy()
    {
        return $this->convertedStandardQuantiy;
    }

    /**
     * Set docQuantity
     *
     * @param float $docQuantity
     *
     * @return NmtProcurePrRow
     */
    public function setDocQuantity($docQuantity)
    {
        $this->docQuantity = $docQuantity;

        return $this;
    }

    /**
     * Get docQuantity
     *
     * @return float
     */
    public function getDocQuantity()
    {
        return $this->docQuantity;
    }

    /**
     * Set docUnit
     *
     * @param string $docUnit
     *
     * @return NmtProcurePrRow
     */
    public function setDocUnit($docUnit)
    {
        $this->docUnit = $docUnit;

        return $this;
    }

    /**
     * Get docUnit
     *
     * @return string
     */
    public function getDocUnit()
    {
        return $this->docUnit;
    }

    /**
     * Set docType
     *
     * @param string $docType
     *
     * @return NmtProcurePrRow
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;

        return $this;
    }

    /**
     * Get docType
     *
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return NmtProcurePrRow
     */
    public function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;

        return $this;
    }

    /**
     * Get reversalBlocked
     *
     * @return boolean
     */
    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    /**
     * Set docVersion
     *
     * @param integer $docVersion
     *
     * @return NmtProcurePrRow
     */
    public function setDocVersion($docVersion)
    {
        $this->docVersion = $docVersion;

        return $this;
    }

    /**
     * Get docVersion
     *
     * @return integer
     */
    public function getDocVersion()
    {
        return $this->docVersion;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtProcurePrRow
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set standardConvertFactor
     *
     * @param integer $standardConvertFactor
     *
     * @return NmtProcurePrRow
     */
    public function setStandardConvertFactor($standardConvertFactor)
    {
        $this->standardConvertFactor = $standardConvertFactor;

        return $this;
    }

    /**
     * Get standardConvertFactor
     *
     * @return integer
     */
    public function getStandardConvertFactor()
    {
        return $this->standardConvertFactor;
    }

    /**
     * Set vendorItemName
     *
     * @param string $vendorItemName
     *
     * @return NmtProcurePrRow
     */
    public function setVendorItemName($vendorItemName)
    {
        $this->vendorItemName = $vendorItemName;

        return $this;
    }

    /**
     * Get vendorItemName
     *
     * @return string
     */
    public function getVendorItemName()
    {
        return $this->vendorItemName;
    }

    /**
     * Set vendorItemCode
     *
     * @param string $vendorItemCode
     *
     * @return NmtProcurePrRow
     */
    public function setVendorItemCode($vendorItemCode)
    {
        $this->vendorItemCode = $vendorItemCode;

        return $this;
    }

    /**
     * Get vendorItemCode
     *
     * @return string
     */
    public function getVendorItemCode()
    {
        return $this->vendorItemCode;
    }

    /**
     * Set clearingDocId
     *
     * @param integer $clearingDocId
     *
     * @return NmtProcurePrRow
     */
    public function setClearingDocId($clearingDocId)
    {
        $this->clearingDocId = $clearingDocId;

        return $this;
    }

    /**
     * Get clearingDocId
     *
     * @return integer
     */
    public function getClearingDocId()
    {
        return $this->clearingDocId;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcurePrRow
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
     * Set pr
     *
     * @param \Application\Entity\NmtProcurePr $pr
     *
     * @return NmtProcurePrRow
     */
    public function setPr(\Application\Entity\NmtProcurePr $pr = null)
    {
        $this->pr = $pr;

        return $this;
    }

    /**
     * Get pr
     *
     * @return \Application\Entity\NmtProcurePr
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * Set item
     *
     * @param \Application\Entity\NmtInventoryItem $item
     *
     * @return NmtProcurePrRow
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
     * Set project
     *
     * @param \Application\Entity\NmtPmProject $project
     *
     * @return NmtProcurePrRow
     */
    public function setProject(\Application\Entity\NmtPmProject $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\Entity\NmtPmProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return NmtProcurePrRow
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
     * Set docUom
     *
     * @param \Application\Entity\NmtApplicationUom $docUom
     *
     * @return NmtProcurePrRow
     */
    public function setDocUom(\Application\Entity\NmtApplicationUom $docUom = null)
    {
        $this->docUom = $docUom;

        return $this;
    }

    /**
     * Get docUom
     *
     * @return \Application\Entity\NmtApplicationUom
     */
    public function getDocUom()
    {
        return $this->docUom;
    }

    /**
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtProcurePrRow
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
}

