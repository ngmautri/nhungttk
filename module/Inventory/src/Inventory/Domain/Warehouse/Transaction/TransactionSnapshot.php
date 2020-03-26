<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionSnapshot extends AbstractValueObject
{

    /**
     *
     * @system_genereted
     */
    public $id;

    /**
     *
     * @system_genereted
     */
    public $token;

    public $currencyIso3;

    public $exchangeRate;

    public $remarks;

    /**
     *
     * @system_genereted
     */
    public $createdOn;

    /**
     *
     * @system_genereted
     */
    public $currentState;

    public $isActive;

    public $trxType;

    /**
     *
     * @system_genereted
     */
    public $lastchangeBy;

    /**
     *
     * @system_genereted
     */
    public $lastchangeOn;

    public $postingDate;

    public $sapDoc;

    public $contractNo;

    public $contractDate;

    public $quotationNo;

    public $quotationDate;

    /**
     *
     * @system_genereted
     */
    public $sysNumber;

    /**
     *
     * @system_genereted
     */
    public $revisionNo;

    public $deliveryMode;

    public $incoterm;

    public $incotermPlace;

    public $paymentTerm;

    public $paymentMethod;

    /**
     *
     * @system_genereted
     */
    public $docStatus;

    public $isDraft;

    /**
     *
     * @system_genereted
     */
    public $workflowStatus;

    /**
     *
     * @system_genereted
     */
    public $transactionStatus;

    public $movementType;

    public $movementDate;

    public $journalMemo;

    public $movementFlow;

    public $movementTypeMemo;

    /**
     *
     * @system_genereted
     */
    public $isPosted;

    /**
     *
     * @system_genereted
     */
    public $isReversed;

    /**
     *
     * @system_genereted
     */
    public $reversalDate;

    /**
     *
     * @system_genereted
     */
    public $reversalDoc;

    public $reversalReason;

    /**
     *
     * @system_genereted
     */
    public $isReversable;

    /**
     *
     * @system_genereted
     */
    public $docType;

    /**
     *
     * @system_genereted
     */
    public $isTransferTransaction;

    /**
     *
     * @system_genereted
     */
    public $reversalBlocked;

    /**
     *
     * @system_genereted
     */
    public $createdBy;

    public $warehouse;

    public $postingPeriod;

    public $currency;

    public $docCurrency;

    public $localCurrency;

    public $targetWarehouse;

    public $sourceLocation;

    public $tartgetLocation;

    public $company;

    public $uuid;
}