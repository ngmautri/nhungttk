<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryMv
 *
 * @ORM\Table(name="nmt_inventory_mv", indexes={@ORM\Index(name="nmt_inventory_mv_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_inventory_mv_FK2_idx", columns={"warehouse_id"}), @ORM\Index(name="nmt_inventory_mv_FK3_idx", columns={"posting_period"}), @ORM\Index(name="nmt_inventory_mv_FK4_idx", columns={"currency_id"}), @ORM\Index(name="nmt_inventory_mv_FK5_idx", columns={"doc_currency_id"}), @ORM\Index(name="nmt_inventory_mv_FK6_idx", columns={"local_currency_id"}), @ORM\Index(name="nmt_inventory_mv_FK7_idx", columns={"target_warehouse"}), @ORM\Index(name="nmt_inventory_mv_FK8_idx", columns={"source_location"}), @ORM\Index(name="nmt_inventory_mv_FK9_idx", columns={"tartget_location"}), @ORM\Index(name="nmt_inventory_mv_FK10_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtInventoryMv
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
     * @ORM\Column(name="currency_iso3", type="string", length=3, nullable=true)
     */
    private $currencyIso3;

    /**
     * @var string
     *
     * @ORM\Column(name="exchange_rate", type="decimal", precision=8, scale=4, nullable=true)
     */
    private $exchangeRate;

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
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="trx_type", type="string", length=45, nullable=true)
     */
    private $trxType;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastchange_by", type="integer", nullable=true)
     */
    private $lastchangeBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posting_date", type="datetime", nullable=true)
     */
    private $postingDate;

    /**
     * @var string
     *
     * @ORM\Column(name="sap_doc", type="string", length=45, nullable=true)
     */
    private $sapDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_no", type="string", length=45, nullable=true)
     */
    private $contractNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contract_date", type="datetime", nullable=true)
     */
    private $contractDate;

    /**
     * @var string
     *
     * @ORM\Column(name="quotation_no", type="string", length=45, nullable=true)
     */
    private $quotationNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="quotation_date", type="datetime", nullable=true)
     */
    private $quotationDate;

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
     * @ORM\Column(name="delivery_mode", type="string", length=45, nullable=true)
     */
    private $deliveryMode;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm", type="string", length=45, nullable=true)
     */
    private $incoterm;

    /**
     * @var string
     *
     * @ORM\Column(name="incoterm_place", type="string", length=100, nullable=true)
     */
    private $incotermPlace;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_term", type="string", length=45, nullable=true)
     */
    private $paymentTerm;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=45, nullable=true)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_status", type="string", length=30, nullable=true)
     */
    private $docStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_status", type="string", length=45, nullable=true)
     */
    private $workflowStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=45, nullable=true)
     */
    private $transactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_type", type="string", length=10, nullable=false)
     */
    private $movementType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="movement_date", type="datetime", nullable=true)
     */
    private $movementDate;

    /**
     * @var string
     *
     * @ORM\Column(name="journal_memo", type="string", length=255, nullable=true)
     */
    private $journalMemo;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_flow", type="string", length=10, nullable=false)
     */
    private $movementFlow;

    /**
     * @var string
     *
     * @ORM\Column(name="movement_type_memo", type="text", length=65535, nullable=true)
     */
    private $movementTypeMemo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_posted", type="boolean", nullable=true)
     */
    private $isPosted;

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
     * @var string
     *
     * @ORM\Column(name="doc_type", type="string", length=10, nullable=true)
     */
    private $docType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_transfer_transaction", type="boolean", nullable=true)
     */
    private $isTransferTransaction;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

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
     * @var \Application\Entity\NmtFinPostingPeriod
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtFinPostingPeriod")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="posting_period", referencedColumnName="id")
     * })
     */
    private $postingPeriod;

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
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doc_currency_id", referencedColumnName="id")
     * })
     */
    private $docCurrency;

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
     * @var \Application\Entity\NmtInventoryWarehouse
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouse")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="target_warehouse", referencedColumnName="id")
     * })
     */
    private $targetWarehouse;

    /**
     * @var \Application\Entity\NmtInventoryWarehouseLocation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouseLocation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_location", referencedColumnName="id")
     * })
     */
    private $sourceLocation;

    /**
     * @var \Application\Entity\NmtInventoryWarehouseLocation
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtInventoryWarehouseLocation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tartget_location", referencedColumnName="id")
     * })
     */
    private $tartgetLocation;



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
     * @return NmtInventoryMv
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
     * Set currencyIso3
     *
     * @param string $currencyIso3
     *
     * @return NmtInventoryMv
     */
    public function setCurrencyIso3($currencyIso3)
    {
        $this->currencyIso3 = $currencyIso3;

        return $this;
    }

    /**
     * Get currencyIso3
     *
     * @return string
     */
    public function getCurrencyIso3()
    {
        return $this->currencyIso3;
    }

    /**
     * Set exchangeRate
     *
     * @param string $exchangeRate
     *
     * @return NmtInventoryMv
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtInventoryMv
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtInventoryMv
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
     * Set trxType
     *
     * @param string $trxType
     *
     * @return NmtInventoryMv
     */
    public function setTrxType($trxType)
    {
        $this->trxType = $trxType;

        return $this;
    }

    /**
     * Get trxType
     *
     * @return string
     */
    public function getTrxType()
    {
        return $this->trxType;
    }

    /**
     * Set lastchangeBy
     *
     * @param integer $lastchangeBy
     *
     * @return NmtInventoryMv
     */
    public function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return integer
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtInventoryMv
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
     * Set postingDate
     *
     * @param \DateTime $postingDate
     *
     * @return NmtInventoryMv
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
     * Set sapDoc
     *
     * @param string $sapDoc
     *
     * @return NmtInventoryMv
     */
    public function setSapDoc($sapDoc)
    {
        $this->sapDoc = $sapDoc;

        return $this;
    }

    /**
     * Get sapDoc
     *
     * @return string
     */
    public function getSapDoc()
    {
        return $this->sapDoc;
    }

    /**
     * Set contractNo
     *
     * @param string $contractNo
     *
     * @return NmtInventoryMv
     */
    public function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;

        return $this;
    }

    /**
     * Get contractNo
     *
     * @return string
     */
    public function getContractNo()
    {
        return $this->contractNo;
    }

    /**
     * Set contractDate
     *
     * @param \DateTime $contractDate
     *
     * @return NmtInventoryMv
     */
    public function setContractDate($contractDate)
    {
        $this->contractDate = $contractDate;

        return $this;
    }

    /**
     * Get contractDate
     *
     * @return \DateTime
     */
    public function getContractDate()
    {
        return $this->contractDate;
    }

    /**
     * Set quotationNo
     *
     * @param string $quotationNo
     *
     * @return NmtInventoryMv
     */
    public function setQuotationNo($quotationNo)
    {
        $this->quotationNo = $quotationNo;

        return $this;
    }

    /**
     * Get quotationNo
     *
     * @return string
     */
    public function getQuotationNo()
    {
        return $this->quotationNo;
    }

    /**
     * Set quotationDate
     *
     * @param \DateTime $quotationDate
     *
     * @return NmtInventoryMv
     */
    public function setQuotationDate($quotationDate)
    {
        $this->quotationDate = $quotationDate;

        return $this;
    }

    /**
     * Get quotationDate
     *
     * @return \DateTime
     */
    public function getQuotationDate()
    {
        return $this->quotationDate;
    }

    /**
     * Set sysNumber
     *
     * @param string $sysNumber
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set deliveryMode
     *
     * @param string $deliveryMode
     *
     * @return NmtInventoryMv
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Get deliveryMode
     *
     * @return string
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * Set incoterm
     *
     * @param string $incoterm
     *
     * @return NmtInventoryMv
     */
    public function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;

        return $this;
    }

    /**
     * Get incoterm
     *
     * @return string
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * Set incotermPlace
     *
     * @param string $incotermPlace
     *
     * @return NmtInventoryMv
     */
    public function setIncotermPlace($incotermPlace)
    {
        $this->incotermPlace = $incotermPlace;

        return $this;
    }

    /**
     * Get incotermPlace
     *
     * @return string
     */
    public function getIncotermPlace()
    {
        return $this->incotermPlace;
    }

    /**
     * Set paymentTerm
     *
     * @param string $paymentTerm
     *
     * @return NmtInventoryMv
     */
    public function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * Get paymentTerm
     *
     * @return string
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return NmtInventoryMv
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set docStatus
     *
     * @param string $docStatus
     *
     * @return NmtInventoryMv
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtInventoryMv
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
     * Set workflowStatus
     *
     * @param string $workflowStatus
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set movementType
     *
     * @param string $movementType
     *
     * @return NmtInventoryMv
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
     * Set movementDate
     *
     * @param \DateTime $movementDate
     *
     * @return NmtInventoryMv
     */
    public function setMovementDate($movementDate)
    {
        $this->movementDate = $movementDate;

        return $this;
    }

    /**
     * Get movementDate
     *
     * @return \DateTime
     */
    public function getMovementDate()
    {
        return $this->movementDate;
    }

    /**
     * Set journalMemo
     *
     * @param string $journalMemo
     *
     * @return NmtInventoryMv
     */
    public function setJournalMemo($journalMemo)
    {
        $this->journalMemo = $journalMemo;

        return $this;
    }

    /**
     * Get journalMemo
     *
     * @return string
     */
    public function getJournalMemo()
    {
        return $this->journalMemo;
    }

    /**
     * Set movementFlow
     *
     * @param string $movementFlow
     *
     * @return NmtInventoryMv
     */
    public function setMovementFlow($movementFlow)
    {
        $this->movementFlow = $movementFlow;

        return $this;
    }

    /**
     * Get movementFlow
     *
     * @return string
     */
    public function getMovementFlow()
    {
        return $this->movementFlow;
    }

    /**
     * Set movementTypeMemo
     *
     * @param string $movementTypeMemo
     *
     * @return NmtInventoryMv
     */
    public function setMovementTypeMemo($movementTypeMemo)
    {
        $this->movementTypeMemo = $movementTypeMemo;

        return $this;
    }

    /**
     * Get movementTypeMemo
     *
     * @return string
     */
    public function getMovementTypeMemo()
    {
        return $this->movementTypeMemo;
    }

    /**
     * Set isPosted
     *
     * @param boolean $isPosted
     *
     * @return NmtInventoryMv
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
     * Set isReversed
     *
     * @param boolean $isReversed
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set docType
     *
     * @param string $docType
     *
     * @return NmtInventoryMv
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
     * Set isTransferTransaction
     *
     * @param boolean $isTransferTransaction
     *
     * @return NmtInventoryMv
     */
    public function setIsTransferTransaction($isTransferTransaction)
    {
        $this->isTransferTransaction = $isTransferTransaction;

        return $this;
    }

    /**
     * Get isTransferTransaction
     *
     * @return boolean
     */
    public function getIsTransferTransaction()
    {
        return $this->isTransferTransaction;
    }

    /**
     * Set reversalBlocked
     *
     * @param boolean $reversalBlocked
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtInventoryMv
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set postingPeriod
     *
     * @param \Application\Entity\NmtFinPostingPeriod $postingPeriod
     *
     * @return NmtInventoryMv
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

    /**
     * Set currency
     *
     * @param \Application\Entity\NmtApplicationCurrency $currency
     *
     * @return NmtInventoryMv
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
     * Set docCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $docCurrency
     *
     * @return NmtInventoryMv
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
     * @return NmtInventoryMv
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
     * Set targetWarehouse
     *
     * @param \Application\Entity\NmtInventoryWarehouse $targetWarehouse
     *
     * @return NmtInventoryMv
     */
    public function setTargetWarehouse(\Application\Entity\NmtInventoryWarehouse $targetWarehouse = null)
    {
        $this->targetWarehouse = $targetWarehouse;

        return $this;
    }

    /**
     * Get targetWarehouse
     *
     * @return \Application\Entity\NmtInventoryWarehouse
     */
    public function getTargetWarehouse()
    {
        return $this->targetWarehouse;
    }

    /**
     * Set sourceLocation
     *
     * @param \Application\Entity\NmtInventoryWarehouseLocation $sourceLocation
     *
     * @return NmtInventoryMv
     */
    public function setSourceLocation(\Application\Entity\NmtInventoryWarehouseLocation $sourceLocation = null)
    {
        $this->sourceLocation = $sourceLocation;

        return $this;
    }

    /**
     * Get sourceLocation
     *
     * @return \Application\Entity\NmtInventoryWarehouseLocation
     */
    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    /**
     * Set tartgetLocation
     *
     * @param \Application\Entity\NmtInventoryWarehouseLocation $tartgetLocation
     *
     * @return NmtInventoryMv
     */
    public function setTartgetLocation(\Application\Entity\NmtInventoryWarehouseLocation $tartgetLocation = null)
    {
        $this->tartgetLocation = $tartgetLocation;

        return $this;
    }

    /**
     * Get tartgetLocation
     *
     * @return \Application\Entity\NmtInventoryWarehouseLocation
     */
    public function getTartgetLocation()
    {
        return $this->tartgetLocation;
    }
}
