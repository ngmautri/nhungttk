<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDetailsSnapshot extends PORowSnapshot
{

    public $vendorName;
    public $poNumber;
    public $docCurrencyISO;
    public $poToken;
    
    
    public $draftGrQuantity;

    public $postedGrQuantity;

    public $confirmedGrBalance;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    public $billedAmount;
    
    public $openAPAmount;
    
    //=======================

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
}