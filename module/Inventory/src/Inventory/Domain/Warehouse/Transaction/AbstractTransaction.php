<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Domain\Shared\AbstractEntity;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTransaction extends AbstractEntity
{

    protected $id;

    protected $token;

    protected $currencyIso3;

    protected $exchangeRate;

    protected $remarks;

    protected $createdOn;

    protected $currentState;

    protected $isActive;

    protected $trxType;

    protected $lastchangeBy;

    protected $lastchangeOn;

    protected $postingDate;

    protected $sapDoc;

    protected $contractNo;

    protected $contractDate;

    protected $quotationNo;

    protected $quotationDate;

    protected $sysNumber;

    protected $revisionNo;

    protected $deliveryMode;

    protected $incoterm;

    protected $incotermPlace;

    protected $paymentTerm;

    protected $paymentMethod;

    protected $docStatus;

    protected $isDraft;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $movementType;

    protected $movementDate;

    protected $journalMemo;

    protected $movementFlow;

    protected $movementTypeMemo;

    protected $isPosted;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalDoc;

    protected $reversalReason;

    protected $isReversable;

    protected $docType;

    protected $isTransferTransaction;

    protected $reversalBlocked;

    protected $createdBy;

    protected $warehouse;

    protected $postingPeriod;

    protected $currency;

    protected $docCurrency;

    protected $localCurrency;

    protected $targetWarehouse;

    protected $sourceLocation;

    protected $tartgetLocation;

    protected $company;

    /**
     *
     * @return NULL|\Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO
     */
    public function makeDTO()
    {
        return TransactionDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public function makeSnapshot()
    {
        return TransactionSnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @param TransactionSnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot)
    {
        if (! $snapshot instanceof TransactionSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->token = $snapshot->token;
        $this->currencyIso3 = $snapshot->currencyIso3;
        $this->exchangeRate = $snapshot->exchangeRate;
        $this->remarks = $snapshot->remarks;
        $this->createdOn = $snapshot->createdOn;
        $this->currentState = $snapshot->currentState;
        $this->isActive = $snapshot->isActive;
        $this->trxType = $snapshot->trxType;
        $this->lastchangeBy = $snapshot->lastchangeBy;
        $this->lastchangeOn = $snapshot->lastchangeOn;
        $this->postingDate = $snapshot->postingDate;
        $this->sapDoc = $snapshot->sapDoc;
        $this->contractNo = $snapshot->contractNo;
        $this->contractDate = $snapshot->contractDate;
        $this->quotationNo = $snapshot->quotationNo;
        $this->quotationDate = $snapshot->quotationDate;
        $this->sysNumber = $snapshot->sysNumber;
        $this->revisionNo = $snapshot->revisionNo;
        $this->deliveryMode = $snapshot->deliveryMode;
        $this->incoterm = $snapshot->incoterm;
        $this->incotermPlace = $snapshot->incotermPlace;
        $this->paymentTerm = $snapshot->paymentTerm;
        $this->paymentMethod = $snapshot->paymentMethod;
        $this->docStatus = $snapshot->docStatus;
        $this->isDraft = $snapshot->isDraft;
        $this->workflowStatus = $snapshot->workflowStatus;
        $this->transactionStatus = $snapshot->transactionStatus;
        $this->movementType = $snapshot->movementType;
        $this->movementDate = $snapshot->movementDate;
        $this->journalMemo = $snapshot->journalMemo;
        $this->movementFlow = $snapshot->movementFlow;
        $this->movementTypeMemo = $snapshot->movementTypeMemo;
        $this->isPosted = $snapshot->isPosted;
        $this->isReversed = $snapshot->isReversed;
        $this->reversalDate = $snapshot->reversalDate;
        $this->reversalDoc = $snapshot->reversalDoc;
        $this->reversalReason = $snapshot->reversalReason;
        $this->isReversable = $snapshot->isReversable;
        $this->docType = $snapshot->docType;
        $this->isTransferTransaction = $snapshot->isTransferTransaction;
        $this->reversalBlocked = $snapshot->reversalBlocked;
        $this->createdBy = $snapshot->createdBy;
        $this->warehouse = $snapshot->warehouse;
        $this->postingPeriod = $snapshot->postingPeriod;
        $this->currency = $snapshot->currency;
        $this->docCurrency = $snapshot->docCurrency;
        $this->localCurrency = $snapshot->localCurrency;
        $this->targetWarehouse = $snapshot->targetWarehouse;
        $this->sourceLocation = $snapshot->sourceLocation;
        $this->tartgetLocation = $snapshot->tartgetLocation;
        $this->company = $snapshot->company;
        
        // set default value
        $this->specify();
    }
    
    abstract public function specify();
    

    public function getId()
    {
        return $this->id;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getCurrencyIso3()
    {
        return $this->currencyIso3;
    }

    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    public function getRemarks()
    {
        return $this->remarks;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getTrxType()
    {
        return $this->trxType;
    }

    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    public function getPostingDate()
    {
        return $this->postingDate;
    }

    public function getSapDoc()
    {
        return $this->sapDoc;
    }

    public function getContractNo()
    {
        return $this->contractNo;
    }

    public function getContractDate()
    {
        return $this->contractDate;
    }

    public function getQuotationNo()
    {
        return $this->quotationNo;
    }

    public function getQuotationDate()
    {
        return $this->quotationDate;
    }

    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    public function getIncoterm()
    {
        return $this->incoterm;
    }

    public function getIncotermPlace()
    {
        return $this->incotermPlace;
    }

    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getDocStatus()
    {
        return $this->docStatus;
    }

    public function getIsDraft()
    {
        return $this->isDraft;
    }

    public function getWorkflowStatus()
    {
        return $this->workflowStatus;
    }

    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    public function getMovementType()
    {
        return $this->movementType;
    }

    public function getMovementDate()
    {
        return $this->movementDate;
    }

    public function getJournalMemo()
    {
        return $this->journalMemo;
    }

    public function getMovementFlow()
    {
        return $this->movementFlow;
    }

    public function getMovementTypeMemo()
    {
        return $this->movementTypeMemo;
    }

    public function getIsPosted()
    {
        return $this->isPosted;
    }

    public function getIsReversed()
    {
        return $this->isReversed;
    }

    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    public function getIsReversable()
    {
        return $this->isReversable;
    }

    public function getDocType()
    {
        return $this->docType;
    }

    public function getIsTransferTransaction()
    {
        return $this->isTransferTransaction;
    }

    public function getReversalBlocked()
    {
        return $this->reversalBlocked;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    public function getPostingPeriod()
    {
        return $this->postingPeriod;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getDocCurrency()
    {
        return $this->docCurrency;
    }

    public function getLocalCurrency()
    {
        return $this->localCurrency;
    }

    public function getTargetWarehouse()
    {
        return $this->targetWarehouse;
    }

    public function getSourceLocation()
    {
        return $this->sourceLocation;
    }

    public function getTartgetLocation()
    {
        return $this->tartgetLocation;
    }
    /**
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param mixed $currencyIso3
     */
    protected function setCurrencyIso3($currencyIso3)
    {
        $this->currencyIso3 = $currencyIso3;
    }

    /**
     * @param mixed $exchangeRate
     */
    protected function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @param mixed $currentState
     */
    protected function setCurrentState($currentState)
    {
        $this->currentState = $currentState;
    }

    /**
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param mixed $trxType
     */
    protected function setTrxType($trxType)
    {
        $this->trxType = $trxType;
    }

    /**
     * @param mixed $lastchangeBy
     */
    protected function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     * @param mixed $lastchangeOn
     */
    protected function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     * @param mixed $postingDate
     */
    protected function setPostingDate($postingDate)
    {
        $this->postingDate = $postingDate;
    }

    /**
     * @param mixed $sapDoc
     */
    protected function setSapDoc($sapDoc)
    {
        $this->sapDoc = $sapDoc;
    }

    /**
     * @param mixed $contractNo
     */
    protected function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;
    }

    /**
     * @param mixed $contractDate
     */
    protected function setContractDate($contractDate)
    {
        $this->contractDate = $contractDate;
    }

    /**
     * @param mixed $quotationNo
     */
    protected function setQuotationNo($quotationNo)
    {
        $this->quotationNo = $quotationNo;
    }

    /**
     * @param mixed $quotationDate
     */
    protected function setQuotationDate($quotationDate)
    {
        $this->quotationDate = $quotationDate;
    }

    /**
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     * @param mixed $deliveryMode
     */
    protected function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * @param mixed $incoterm
     */
    protected function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;
    }

    /**
     * @param mixed $incotermPlace
     */
    protected function setIncotermPlace($incotermPlace)
    {
        $this->incotermPlace = $incotermPlace;
    }

    /**
     * @param mixed $paymentTerm
     */
    protected function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;
    }

    /**
     * @param mixed $paymentMethod
     */
    protected function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param mixed $docStatus
     */
    protected function setDocStatus($docStatus)
    {
        $this->docStatus = $docStatus;
    }

    /**
     * @param mixed $isDraft
     */
    protected function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;
    }

    /**
     * @param mixed $workflowStatus
     */
    protected function setWorkflowStatus($workflowStatus)
    {
        $this->workflowStatus = $workflowStatus;
    }

    /**
     * @param mixed $transactionStatus
     */
    protected function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;
    }

    /**
     * @param mixed $movementType
     */
    protected function setMovementType($movementType)
    {
        $this->movementType = $movementType;
    }

    /**
     * @param mixed $movementDate
     */
    protected function setMovementDate($movementDate)
    {
        $this->movementDate = $movementDate;
    }

    /**
     * @param mixed $journalMemo
     */
    protected function setJournalMemo($journalMemo)
    {
        $this->journalMemo = $journalMemo;
    }

    /**
     * @param mixed $movementFlow
     */
    protected function setMovementFlow($movementFlow)
    {
        $this->movementFlow = $movementFlow;
    }

    /**
     * @param mixed $movementTypeMemo
     */
    protected function setMovementTypeMemo($movementTypeMemo)
    {
        $this->movementTypeMemo = $movementTypeMemo;
    }

    /**
     * @param mixed $isPosted
     */
    protected function setIsPosted($isPosted)
    {
        $this->isPosted = $isPosted;
    }

    /**
     * @param mixed $isReversed
     */
    protected function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     * @param mixed $reversalDate
     */
    protected function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     * @param mixed $isReversable
     */
    protected function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     * @param mixed $docType
     */
    protected function setDocType($docType)
    {
        $this->docType = $docType;
    }

    /**
     * @param mixed $isTransferTransaction
     */
    protected function setIsTransferTransaction($isTransferTransaction)
    {
        $this->isTransferTransaction = $isTransferTransaction;
    }

    /**
     * @param mixed $reversalBlocked
     */
    protected function setReversalBlocked($reversalBlocked)
    {
        $this->reversalBlocked = $reversalBlocked;
    }

    /**
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @param mixed $warehouse
     */
    protected function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

    /**
     * @param mixed $postingPeriod
     */
    protected function setPostingPeriod($postingPeriod)
    {
        $this->postingPeriod = $postingPeriod;
    }

    /**
     * @param mixed $currency
     */
    protected function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $docCurrency
     */
    protected function setDocCurrency($docCurrency)
    {
        $this->docCurrency = $docCurrency;
    }

    /**
     * @param mixed $localCurrency
     */
    protected function setLocalCurrency($localCurrency)
    {
        $this->localCurrency = $localCurrency;
    }

    /**
     * @param mixed $targetWarehouse
     */
    protected function setTargetWarehouse($targetWarehouse)
    {
        $this->targetWarehouse = $targetWarehouse;
    }

    /**
     * @param mixed $sourceLocation
     */
    protected function setSourceLocation($sourceLocation)
    {
        $this->sourceLocation = $sourceLocation;
    }

    /**
     * @param mixed $tartgetLocation
     */
    protected function setTartgetLocation($tartgetLocation)
    {
        $this->tartgetLocation = $tartgetLocation;
    }

    /**
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
    }

}