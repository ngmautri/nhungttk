<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocRowDetailsSnapshot extends APDocRowSnapshot
{
 
    
    public $invoiceToken;
    public $sapNumber;
    
    public $vendorName;
    public $localCurrencyId;
    public $docCurrencyId;
    public $docCurrencyISO;
    public $exchangeRate;
    public $apDocStatus;
    
    public $postingYear;
    public $postingMonth;
  
    public $prId;
    public $prToken;
    public $prNumber;
    public $prSysNumber;
    public $prDate;
    
    public $poId;
    public $poToken;
    public $poNumber;
    public $poSysNumber;
    public $poDate;
    
    public $itemName;   
    public $itemToken;    
    public $itemNumber;
    public $itemSKU;    
    public $itemSKU1;
    public $itemSKU2;
   
    
}