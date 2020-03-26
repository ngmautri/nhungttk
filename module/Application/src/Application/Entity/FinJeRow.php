<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinJeRow
 *
 * @ORM\Table(name="fin_je_row", indexes={@ORM\Index(name="fin_je_row_FK1_idx", columns={"je_id"}), @ORM\Index(name="fin_je_row_FK2_idx", columns={"GL_account_id"}), @ORM\Index(name="fin_je_row_FK3_idx", columns={"created_by"}), @ORM\Index(name="fin_je_row_FK4_idx", columns={"sub_account_id"}), @ORM\Index(name="fin_je_row_FK5_idx", columns={"cost_center_id"}), @ORM\Index(name="fin_je_row_FK6_idx", columns={"gr_row_id"}), @ORM\Index(name="fin_je_row_FK7_idx", columns={"ap_row_id"}), @ORM\Index(name="fin_je_row_FK8_idx", columns={"ap_id"}), @ORM\Index(name="fin_je_row_FK9_idx", columns={"gr_id"})})
 * @ORM\Entity
 */
class FinJeRow
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
     * @ORM\Column(name="posting_key", type="string", length=5, nullable=false)
     */
    private $postingKey;

    /**
     * @var string
     *
     * @ORM\Column(name="posting_code", type="string", length=5, nullable=false)
     */
    private $postingCode;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $docAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="local_amount", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $localAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

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
     * @ORM\Column(name="je_note", type="string", length=45, nullable=true)
     */
    private $jeNote;

    /**
     * @var string
     *
     * @ORM\Column(name="je_description", type="string", length=255, nullable=true)
     */
    private $jeDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="sys_number", type="string", length=45, nullable=true)
     */
    private $sysNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="je_memo", type="string", length=100, nullable=true)
     */
    private $jeMemo;

    /**
     * @var string
     *
     * @ORM\Column(name="je_gr_memo", type="string", length=100, nullable=true)
     */
    private $jeGrMemo;

    /**
     * @var string
     *
     * @ORM\Column(name="je_ap_memo", type="string", length=100, nullable=true)
     */
    private $jeApMemo;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

    /**
     * @var \Application\Entity\FinJe
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinJe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="je_id", referencedColumnName="id")
     * })
     */
    private $je;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;

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
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sub_account_id", referencedColumnName="id")
     * })
     */
    private $subAccount;

    /**
     * @var \Application\Entity\FinCostCenter
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinCostCenter")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cost_center_id", referencedColumnName="id")
     * })
     */
    private $costCenter;

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
     * @var \Application\Entity\FinVendorInvoice
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinVendorInvoice")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ap_id", referencedColumnName="id")
     * })
     */
    private $ap;

    /**
     * @var \Application\Entity\NmtProcureGr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcureGr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gr_id", referencedColumnName="id")
     * })
     */
    private $gr;



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
     * Set postingKey
     *
     * @param string $postingKey
     *
     * @return FinJeRow
     */
    public function setPostingKey($postingKey)
    {
        $this->postingKey = $postingKey;

        return $this;
    }

    /**
     * Get postingKey
     *
     * @return string
     */
    public function getPostingKey()
    {
        return $this->postingKey;
    }

    /**
     * Set postingCode
     *
     * @param string $postingCode
     *
     * @return FinJeRow
     */
    public function setPostingCode($postingCode)
    {
        $this->postingCode = $postingCode;

        return $this;
    }

    /**
     * Get postingCode
     *
     * @return string
     */
    public function getPostingCode()
    {
        return $this->postingCode;
    }

    /**
     * Set docAmount
     *
     * @param string $docAmount
     *
     * @return FinJeRow
     */
    public function setDocAmount($docAmount)
    {
        $this->docAmount = $docAmount;

        return $this;
    }

    /**
     * Get docAmount
     *
     * @return string
     */
    public function getDocAmount()
    {
        return $this->docAmount;
    }

    /**
     * Set localAmount
     *
     * @param string $localAmount
     *
     * @return FinJeRow
     */
    public function setLocalAmount($localAmount)
    {
        $this->localAmount = $localAmount;

        return $this;
    }

    /**
     * Get localAmount
     *
     * @return string
     */
    public function getLocalAmount()
    {
        return $this->localAmount;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinJeRow
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return FinJeRow
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
     * @return FinJeRow
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
     * @return FinJeRow
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
     * Set jeNote
     *
     * @param string $jeNote
     *
     * @return FinJeRow
     */
    public function setJeNote($jeNote)
    {
        $this->jeNote = $jeNote;

        return $this;
    }

    /**
     * Get jeNote
     *
     * @return string
     */
    public function getJeNote()
    {
        return $this->jeNote;
    }

    /**
     * Set jeDescription
     *
     * @param string $jeDescription
     *
     * @return FinJeRow
     */
    public function setJeDescription($jeDescription)
    {
        $this->jeDescription = $jeDescription;

        return $this;
    }

    /**
     * Get jeDescription
     *
     * @return string
     */
    public function getJeDescription()
    {
        return $this->jeDescription;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return FinJeRow
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return FinJeRow
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
     * Set jeMemo
     *
     * @param string $jeMemo
     *
     * @return FinJeRow
     */
    public function setJeMemo($jeMemo)
    {
        $this->jeMemo = $jeMemo;

        return $this;
    }

    /**
     * Get jeMemo
     *
     * @return string
     */
    public function getJeMemo()
    {
        return $this->jeMemo;
    }

    /**
     * Set jeGrMemo
     *
     * @param string $jeGrMemo
     *
     * @return FinJeRow
     */
    public function setJeGrMemo($jeGrMemo)
    {
        $this->jeGrMemo = $jeGrMemo;

        return $this;
    }

    /**
     * Get jeGrMemo
     *
     * @return string
     */
    public function getJeGrMemo()
    {
        return $this->jeGrMemo;
    }

    /**
     * Set jeApMemo
     *
     * @param string $jeApMemo
     *
     * @return FinJeRow
     */
    public function setJeApMemo($jeApMemo)
    {
        $this->jeApMemo = $jeApMemo;

        return $this;
    }

    /**
     * Get jeApMemo
     *
     * @return string
     */
    public function getJeApMemo()
    {
        return $this->jeApMemo;
    }

    /**
     * Set docType
     *
     * @param string $docType
     *
     * @return FinJeRow
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
     * @return FinJeRow
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
     * Set je
     *
     * @param \Application\Entity\FinJe $je
     *
     * @return FinJeRow
     */
    public function setJe(\Application\Entity\FinJe $je = null)
    {
        $this->je = $je;

        return $this;
    }

    /**
     * Get je
     *
     * @return \Application\Entity\FinJe
     */
    public function getJe()
    {
        return $this->je;
    }

    /**
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return FinJeRow
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinJeRow
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
     * Set subAccount
     *
     * @param \Application\Entity\FinAccount $subAccount
     *
     * @return FinJeRow
     */
    public function setSubAccount(\Application\Entity\FinAccount $subAccount = null)
    {
        $this->subAccount = $subAccount;

        return $this;
    }

    /**
     * Get subAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getSubAccount()
    {
        return $this->subAccount;
    }

    /**
     * Set costCenter
     *
     * @param \Application\Entity\FinCostCenter $costCenter
     *
     * @return FinJeRow
     */
    public function setCostCenter(\Application\Entity\FinCostCenter $costCenter = null)
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * Get costCenter
     *
     * @return \Application\Entity\FinCostCenter
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * Set grRow
     *
     * @param \Application\Entity\NmtProcureGrRow $grRow
     *
     * @return FinJeRow
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
     * @return FinJeRow
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
     * Set ap
     *
     * @param \Application\Entity\FinVendorInvoice $ap
     *
     * @return FinJeRow
     */
    public function setAp(\Application\Entity\FinVendorInvoice $ap = null)
    {
        $this->ap = $ap;

        return $this;
    }

    /**
     * Get ap
     *
     * @return \Application\Entity\FinVendorInvoice
     */
    public function getAp()
    {
        return $this->ap;
    }

    /**
     * Set gr
     *
     * @param \Application\Entity\NmtProcureGr $gr
     *
     * @return FinJeRow
     */
    public function setGr(\Application\Entity\NmtProcureGr $gr = null)
    {
        $this->gr = $gr;

        return $this;
    }

    /**
     * Get gr
     *
     * @return \Application\Entity\NmtProcureGr
     */
    public function getGr()
    {
        return $this->gr;
    }
}
