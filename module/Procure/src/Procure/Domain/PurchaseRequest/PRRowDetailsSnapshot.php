<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRowDetailsSnapshot extends PRRowSnapshot
{
    public $prName;

    public $prYear;

    public $draftPOQuantity;

    public $postedPOQuantity;

    public $draftStockGRQuantity;

    public $postedStockGRQuantity;

    public $draftGrQuantity;

    public $postedGrQuantity;

    public $confirmedGrBalance;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    public $billedAmount;

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

    public $itemToken;

    public $itemCheckSum;

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

    public $lastVendorName;

    public $lastUnitPrice;

    public $lastCurrency;
}