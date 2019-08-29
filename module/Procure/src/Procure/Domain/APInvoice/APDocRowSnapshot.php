<?php
namespace Procure\Domain\APInvoice;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocRowSnapshot extends AbstractValueObject
{

    public $id;

    public $rowNumber;

    public $token;

    public $quantity;

    public $unitPrice;

    public $netAmount;

    public $unit;

    public $itemUnit;

    public $conversionFactor;

    public $converstionText;

    public $taxRate;

    public $remarks;

    public $isActive;

    public $createdOn;

    public $lastchangeOn;

    public $currentState;

    public $vendorItemCode;

    public $traceStock;

    public $grossAmount;

    public $taxAmount;

    public $faRemarks;

    public $rowIdentifer;

    public $discountRate;

    public $revisionNo;

    public $localUnitPrice;

    public $docUnitPrice;

    public $exwUnitPrice;

    public $exwCurrency;

    public $localNetAmount;

    public $localGrossAmount;

    public $docStatus;

    public $workflowStatus;

    public $transactionType;

    public $isDraft;

    public $isPosted;

    public $transactionStatus;

    public $totalExwPrice;

    public $convertFactorPurchase;

    public $convertedPurchaseQuantity;

    public $convertedStockQuantity;

    public $convertedStockUnitPrice;

    public $convertedStandardQuantity;

    public $convertedStandardUnitPrice;

    public $docQuantity;

    public $docUnit;

    public $convertedPurchaseUnitPrice;

    public $isReversed;

    public $reversalDate;

    public $reversalReason;

    public $reversalDoc;

    public $isReversable;

    public $docType;

    public $descriptionText;

    public $vendorItemName;

    public $reversalBlocked;

    public $invoice;

    public $glAccount;

    public $costCenter;

    public $docUom;

    public $prRow;

    public $createdBy;

    public $warehouse;

    public $lastchangeBy;

    public $poRow;

    public $item;

    public $grRow;
}