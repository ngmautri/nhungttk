<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PODetailsSnapshot extends POSnapshot
{
    public $totalRows;
    public $totalActiveRows;
    public $maxRowNumber;
    public $netAmount;
    public $taxAmount;
    public $grossAmount;
    public $discountAmount;
    
    public $billedAmount;
    public $completedRows;
}