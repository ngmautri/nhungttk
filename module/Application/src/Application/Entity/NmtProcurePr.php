<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePr
 *
 * @ORM\Table(name="nmt_procure_pr", indexes={@ORM\Index(name="nmt_procure_pr_KF1_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_pr_KF2_idx", columns={"lastchange_by"}), @ORM\Index(name="nmt_procure_pr_KF3_idx", columns={"department_id"}), @ORM\Index(name="nmt_procure_pr_KF4_idx", columns={"company_id"}), @ORM\Index(name="nmt_procure_pr_KF5_idx", columns={"warehouse_id"})})
 * @ORM\Entity
 */
class NmtProcurePr
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
     * @ORM\Column(name="pr_auto_number", type="string", length=45, nullable=true)
     */
    private $prAutoNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_number", type="string", length=45, nullable=false)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_name", type="string", length=45, nullable=false)
     */
    private $prName;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=100, nullable=true)
     */
    private $keywords;

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
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

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
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

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
     * @ORM\Column(name="submitted_on", type="datetime", nullable=true)
     */
    private $submittedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_row_manual", type="integer", nullable=true)
     */
    private $totalRowManual;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=30, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=45, nullable=true)
     */
    private $docType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_number", type="string", length=100, nullable=true)
     */
    private $docNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="doc_date", type="datetime", nullable=true)
     */
    private $docDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="base_doc_id", type="integer", nullable=true)
     */
    private $baseDocId;

    /**
     * @var string
     *
     * @ORM\Column(name="base_doc_type", type="string", length=45, nullable=true)
     */
    private $baseDocType;

    /**
     * @var integer
     *
     * @ORM\Column(name="target_doc_id", type="integer", nullable=true)
     */
    private $targetDocId;

    /**
     * @var string
     *
     * @ORM\Column(name="target_doc_type", type="string", length=45, nullable=true)
     */
    private $targetDocType;

    /**
     * @var integer
     *
     * @ORM\Column(name="clearing_doc_id", type="integer", nullable=true)
     */
    private $clearingDocId;

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
     * @var \Application\Entity\NmtApplicationDepartment
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationDepartment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="node_id")
     * })
     */
    private $department;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prAutoNumber
     *
     * @param string $prAutoNumber
     *
     * @return NmtProcurePr
     */
    public function setPrAutoNumber($prAutoNumber)
    {
        $this->prAutoNumber = $prAutoNumber;

        return $this;
    }

    /**
     * Get prAutoNumber
     *
     * @return string
     */
    public function getPrAutoNumber()
    {
        return $this->prAutoNumber;
    }

    /**
     * Set prNumber
     *
     * @param string $prNumber
     *
     * @return NmtProcurePr
     */
    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;

        return $this;
    }

    /**
     * Get prNumber
     *
     * @return string
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     * Set prName
     *
     * @param string $prName
     *
     * @return NmtProcurePr
     */
    public function setPrName($prName)
    {
        $this->prName = $prName;

        return $this;
    }

    /**
     * Get prName
     *
     * @return string
     */
    public function getPrName()
    {
        return $this->prName;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return NmtProcurePr
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtProcurePr
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set status
     *
     * @param string $status
     *
     * @return NmtProcurePr
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set submittedOn
     *
     * @param \DateTime $submittedOn
     *
     * @return NmtProcurePr
     */
    public function setSubmittedOn($submittedOn)
    {
        $this->submittedOn = $submittedOn;

        return $this;
    }

    /**
     * Get submittedOn
     *
     * @return \DateTime
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtProcurePr
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
     * Set totalRowManual
     *
     * @param integer $totalRowManual
     *
     * @return NmtProcurePr
     */
    public function setTotalRowManual($totalRowManual)
    {
        $this->totalRowManual = $totalRowManual;

        return $this;
    }

    /**
     * Get totalRowManual
     *
     * @return integer
     */
    public function getTotalRowManual()
    {
        return $this->totalRowManual;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set docType
     *
     * @param string $docType
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtProcurePr
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
     * Set docNumber
     *
     * @param string $docNumber
     *
     * @return NmtProcurePr
     */
    public function setDocNumber($docNumber)
    {
        $this->docNumber = $docNumber;

        return $this;
    }

    /**
     * Get docNumber
     *
     * @return string
     */
    public function getDocNumber()
    {
        return $this->docNumber;
    }

    /**
     * Set docDate
     *
     * @param \DateTime $docDate
     *
     * @return NmtProcurePr
     */
    public function setDocDate($docDate)
    {
        $this->docDate = $docDate;

        return $this;
    }

    /**
     * Get docDate
     *
     * @return \DateTime
     */
    public function getDocDate()
    {
        return $this->docDate;
    }

    /**
     * Set baseDocId
     *
     * @param integer $baseDocId
     *
     * @return NmtProcurePr
     */
    public function setBaseDocId($baseDocId)
    {
        $this->baseDocId = $baseDocId;

        return $this;
    }

    /**
     * Get baseDocId
     *
     * @return integer
     */
    public function getBaseDocId()
    {
        return $this->baseDocId;
    }

    /**
     * Set baseDocType
     *
     * @param string $baseDocType
     *
     * @return NmtProcurePr
     */
    public function setBaseDocType($baseDocType)
    {
        $this->baseDocType = $baseDocType;

        return $this;
    }

    /**
     * Get baseDocType
     *
     * @return string
     */
    public function getBaseDocType()
    {
        return $this->baseDocType;
    }

    /**
     * Set targetDocId
     *
     * @param integer $targetDocId
     *
     * @return NmtProcurePr
     */
    public function setTargetDocId($targetDocId)
    {
        $this->targetDocId = $targetDocId;

        return $this;
    }

    /**
     * Get targetDocId
     *
     * @return integer
     */
    public function getTargetDocId()
    {
        return $this->targetDocId;
    }

    /**
     * Set targetDocType
     *
     * @param string $targetDocType
     *
     * @return NmtProcurePr
     */
    public function setTargetDocType($targetDocType)
    {
        $this->targetDocType = $targetDocType;

        return $this;
    }

    /**
     * Get targetDocType
     *
     * @return string
     */
    public function getTargetDocType()
    {
        return $this->targetDocType;
    }

    /**
     * Set clearingDocId
     *
     * @param integer $clearingDocId
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set department
     *
     * @param \Application\Entity\NmtApplicationDepartment $department
     *
     * @return NmtProcurePr
     */
    public function setDepartment(\Application\Entity\NmtApplicationDepartment $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\NmtApplicationDepartment
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtProcurePr
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set warehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     *
     * @return NmtProcurePr
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
