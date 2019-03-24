<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinJe
 *
 * @ORM\Table(name="fin_je", indexes={@ORM\Index(name="fin_je_FK1_idx", columns={"currency_id"}), @ORM\Index(name="fin_je_FK2_idx", columns={"created_by"}), @ORM\Index(name="fin_je_FK3_idx", columns={"last_change_by"}), @ORM\Index(name="fin_je_FK4_idx", columns={"local_currency_id"}), @ORM\Index(name="fin_je_FK5_idx", columns={"sys_currency_id"}), @ORM\Index(name="fin_je_FK6_idx", columns={"doc_currency_id"}), @ORM\Index(name="fin_je_FK7_idx", columns={"posting_period_id"})})
 * @ORM\Entity
 */
class FinJe
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
     * @ORM\Column(name="je_type", type="string", length=45, nullable=true)
     */
    private $jeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="document_date", type="datetime", nullable=true)
     */
    private $documentDate;

    /**
     * @var string
     *
     * @ORM\Column(name="je_reference", type="string", length=45, nullable=true)
     */
    private $jeReference;

    /**
     * @var string
     *
     * @ORM\Column(name="je_note", type="string", length=255, nullable=true)
     */
    private $jeNote;

    /**
     * @var string
     *
     * @ORM\Column(name="je_remarks", type="text", length=65535, nullable=true)
     */
    private $jeRemarks;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=14, scale=4, nullable=true)
     */
    private $exchangeRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=45, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_type", type="string", length=45, nullable=true)
     */
    private $transactionType;

    /**
     * @var string
     *
     * @ORM\Column(name="je_status", type="string", length=45, nullable=true)
     */
    private $jeStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

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
     * @ORM\Column(name="reversed_doc", type="integer", nullable=true)
     */
    private $reversedDoc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversal_blocked", type="boolean", nullable=true)
     */
    private $reversalBlocked;

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
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="local_currency_id", referencedColumnName="id")
     * })
     */
    private $localCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sys_currency_id", referencedColumnName="id")
     * })
     */
    private $sysCurrency;

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
     * @return FinJe
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
     * Set jeType
     *
     * @param string $jeType
     *
     * @return FinJe
     */
    public function setJeType($jeType)
    {
        $this->jeType = $jeType;

        return $this;
    }

    /**
     * Get jeType
     *
     * @return string
     */
    public function getJeType()
    {
        return $this->jeType;
    }

    /**
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return FinJe
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
     * Set documentDate
     *
     * @param \DateTime $documentDate
     *
     * @return FinJe
     */
    public function setDocumentDate($documentDate)
    {
        $this->documentDate = $documentDate;

        return $this;
    }

    /**
     * Get documentDate
     *
     * @return \DateTime
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * Set jeReference
     *
     * @param string $jeReference
     *
     * @return FinJe
     */
    public function setJeReference($jeReference)
    {
        $this->jeReference = $jeReference;

        return $this;
    }

    /**
     * Get jeReference
     *
     * @return string
     */
    public function getJeReference()
    {
        return $this->jeReference;
    }

    /**
     * Set jeNote
     *
     * @param string $jeNote
     *
     * @return FinJe
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
     * Set jeRemarks
     *
     * @param string $jeRemarks
     *
     * @return FinJe
     */
    public function setJeRemarks($jeRemarks)
    {
        $this->jeRemarks = $jeRemarks;

        return $this;
    }

    /**
     * Get jeRemarks
     *
     * @return string
     */
    public function getJeRemarks()
    {
        return $this->jeRemarks;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return FinJe
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinJe
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
     * Set docType
     *
     * @param string $docType
     *
     * @return FinJe
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
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return FinJe
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
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return FinJe
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
     * Set transactionType
     *
     * @param string $transactionType
     *
     * @return FinJe
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
     * Set jeStatus
     *
     * @param string $jeStatus
     *
     * @return FinJe
     */
    public function setJeStatus($jeStatus)
    {
        $this->jeStatus = $jeStatus;

        return $this;
    }

    /**
     * Get jeStatus
     *
     * @return string
     */
    public function getJeStatus()
    {
        return $this->jeStatus;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return FinJe
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
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return FinJe
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
     * @return FinJe
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
     * Set sourceId
     *
     * @param integer $sourceId
     *
     * @return FinJe
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
     * @return FinJe
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
     * @return FinJe
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
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return FinJe
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
     * @return FinJe
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
     * Set reversedDoc
     *
     * @param integer $reversedDoc
     *
     * @return FinJe
     */
    public function setReversedDoc($reversedDoc)
    {
        $this->reversedDoc = $reversedDoc;

        return $this;
    }

    /**
     * Get reversedDoc
     *
     * @return integer
     */
    public function getReversedDoc()
    {
        return $this->reversedDoc;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return FinJe
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
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return FinJe
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinJe
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return FinJe
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
     * Set localCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $localCurrency
     *
     * @return FinJe
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
     * Set sysCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $sysCurrency
     *
     * @return FinJe
     */
    public function setSysCurrency(\Application\Entity\NmtApplicationCurrency $sysCurrency = null)
    {
        $this->sysCurrency = $sysCurrency;

        return $this;
    }

    /**
     * Get sysCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getSysCurrency()
    {
        return $this->sysCurrency;
    }

    /**
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return FinJe
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
     * @return FinJe
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
}
