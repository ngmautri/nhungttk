<?php
namespace Procure\Domain\APInvoice;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoiceSnapshot extends AbstractValueObject
{

    
    public $id;
    
    public $token;
    
    public $vendorName;
    
    public $invoiceNo;
    
    public $invoiceDate;
    
    public $currencyIso3;
    
    public $exchangeRate;
    
    public $remarks;
    
    public $createdOn;
    
    public $currentState;
    
    public $isActive;
    
    public $trxType;
    
    public $lastchangeOn;
    
    public $postingDate;
    
    public $grDate;
    
    public $sapDoc;
    
    public $contractNo;
    
    public $contractDate;
    
    public $quotationNo;
    
    public $quotationDate;
    
    public $sysNumber;
    
    public $revisionNo;
    
    public $currentStatus;
    
    public $docStatus;
    
    public $workflowStatus;
    
    public $paymentTerm;
    
    public $transactionType;
    
    public $isDraft;
    
    public $isPosted;
    
    public $transactionStatus;
    
    public $incoterm;
    
    public $incotermPlace;
    
    public $isReversed;
    
    public $reversalDate;
    
    public $reversalDoc;
    
    public $reversalReason;
    
    public $paymentStatus;
    
    public $isReversable;
    
    public $docType;
    
    public $totalDocValue;
    
    public $totalDocTax;
    
    public $totalDocDiscount;
    
    public $totalLocalValue;
    
    public $totalLocalTax;
    
    public $totalLocalDiscount;
    
    public $reversalBlocked;
    
    public $uuid;
    
    public $vendor;
    
    public $procureGr;
    
    public $localCurrency;
    
    public $docCurrency;
    
    public $postingPeriod;
    
    public $incoterm2;
    
    public $pmtTerm;
    
    public $warehouse;
    
    public $createdBy;
    
    public $lastchangeBy;
    
    public $currency;
    
    public $po;
    
    public $company;
    
    public $paymentMethod;
    
    public $inventoryGr;
}