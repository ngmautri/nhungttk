<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRowSnapshot extends AbstractValueObject
{
    public $id;
    
    public $rowNumber;
    
    public $rowIdentifer;
    
    public $token;
    
    public $checksum;
    
    public $priority;
    
    public $rowName;
    
    public $rowDescription;
    
    public $rowCode;
    
    public $rowUnit;
    
    public $conversionFactor;
    
    public $conversionText;
    
    public $quantity;
    
    public $edt;
    
    public $isDraft;
    
    public $isActive;
    
    public $createdOn;
    
    public $remarks;
    
    public $lastChangeOn;
    
    public $currentState;
    
    public $faRemarks;
    
    public $revisionNo;
    
    public $docStatus;
    
    public $workflowStatus;
    
    public $transactionStatus;
    
    public $convertedStockQuantity;
    
    public $convertedStandardQuantiy;
    
    public $docQuantity;
    
    public $docUnit;
    
    public $docType;
    
    public $reversalBlocked;
    
    public $createdBy;
    
    public $pr;
    
    public $item;
    
    public $project;
    
    public $lastChangeBy;
    
    public $docUom;
    
    public $warehouse;

}