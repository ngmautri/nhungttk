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

    public $reversalReason;
    public $reversalDoc;
    public $isReversable;
    public $grRow;
    public $poRow;
    public $itemToken;
    public $itemChecksum;
    public $itemName;
    public $itemName1;
    public $itemSKU;
    public $itemSKU1;
    public $itemSKU2;
    public $itemUUID;
    public $itemSysNumber;
    public $itemStandardUnit;
    public $itemStandardUnitName;
    public $itemVersion;
    public $pr;
    public $prToken;
    public $prChecksum;
    public $prNumber;
    public $prSysNumber;
    public $prRowIndentifer;
    public $prRowCode;
    public $prRowName;
    public $prRowConvertFactor;
    public $prRowUnit;
    public $prRowVersion;
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
    public $targetObject;
    public $sourceObject;
    public $targetObjectId;
    public $sourceObjectId;
    public $docStatus;
    public $workflowStatus;
    public $transactionStatus;
    public $isPosted;
    public $isDraft;
    public $exwUnitPrice;
    public $totalExwPrice;
    public $convertFactorPurchase;
    public $convertedPurchaseQuantity;
    public $convertedStandardQuantity;
    public $convertedStockQuantity;
    public $convertedStandardUnitPrice;
    public $convertedStockUnitPrice;
    public $docQuantity;
    public $docUnit;
    public $docUnitPrice;
    public $convertedPurchaseUnitPrice;
    public $docType;
    public $descriptionText;
    public $vendorItemName;
    public $reversalBlocked;
    public $invoice;
    public $lastchangeBy;
    public $prRow;
    public $createdBy;
    public $warehouse;
    public $po;
    public $item;
    public $docUom;
    public $docVersion;
    public $uuid;
    public $localUnitPrice;
    public $exwCurrency;
    public $localNetAmount;
    public $localGrossAmount;
    public $transactionType;
    public $isReversed;
    public $reversalDate;
    public $glAccount;
    public $costCenter;
    
}