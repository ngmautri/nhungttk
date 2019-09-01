<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowDetailsSnapshot extends PORowSnapshot
{

    public $draftGrQuantity;

    public $postedGrQuantity;

    public $confirmedGrBalance;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    public $billedAmount;

    public $prNumber;

    public $prSysNumber;

    public $prRowIndentifer;

    public $prRowCode;

    public $prRowName;

    public $prRowConvertFactor;

    public $prRowUnit;
    
    public $prRowVersion;

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