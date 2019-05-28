<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Abstract Class GenericWarehouseTransaction extends AbstractEntity
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
    
    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        
        /**
         *
         * @var Notification $notification
         */
        $notification = $this->validate();
        
        if($notification == null)
            return false;
        
        return ! $notification->hasErrors();
    }
    
    abstract public function validate();
    
    /**
     * 
     * @param GenericWarehouseTransaction $this
     */
    public function makeDTO($this){
        
    }
    
    /**
     * 
     * @param GenericWarehouseTransaction $this
     */
    public function makeSnapshot($this){
        
    }
    
    /**
     * 
     * @param WarehouseTransactionSnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot){
        if (! $snapshot instanceof $snapshot)
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
    }
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

    
    
}