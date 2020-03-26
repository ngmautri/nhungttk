<?php
namespace Procure\Domain;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractHeader extends AbstractEntity implements AggregateRootInterface
{

    // +++++++++++++++++++ ADTIONAL +++++++++++++++++++++
    
    protected $paymentTermName;

    protected $paymentTermCode;

    protected $warehouseName;

    protected $warehouseCode;

    protected $paymentMethodName;

    protected $paymentMethodCode;

    protected $incotermCode;

    protected $incotermName;

    protected $createdByName;

    protected $lastChangedByName;

    protected $totalRows;

    protected $totalActiveRows;

    protected $maxRowNumber;

    protected $netAmount;

    protected $taxAmount;

    protected $grossAmount;

    protected $discountAmount;

    protected $billedAmount;

    protected $completedRows;

    protected $openAPAmount;

    protected $id;

    protected $token;

    protected $vendorName;

    protected $invoiceNo;

    protected $invoiceDate;

    protected $currencyIso3;

    protected $exchangeRate;

    protected $remarks;

    protected $createdOn;

    protected $currentState;

    protected $isActive;

    protected $trxType;

    protected $lastchangeOn;

    protected $postingDate;

    protected $grDate;

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

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $docType;

    protected $paymentStatus;

    protected $totalDocValue;

    protected $totalDocTax;

    protected $totalDocDiscount;

    protected $totalLocalValue;

    protected $totalLocalTax;

    protected $totalLocalDiscount;

    protected $reversalBlocked;

    protected $uuid;

    protected $vendor;

    protected $pmtTerm;

    protected $company;

    protected $warehouse;

    protected $createdBy;

    protected $lastchangeBy;

    protected $currency;

    protected $paymentMethod;

    protected $localCurrency;

    protected $docCurrency;

    protected $incoterm2;

    protected $docVersion;
}