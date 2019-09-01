<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PODetailsSnapshot extends POSnapshot
{

    public $paymentTermName;

    public $paymentTermCode;

    public $warehouseName;

    public $warehouseCode;

    public $paymentMethodName;

    public $paymentMethodCode;

    public $incotermCode;

    public $incotermName;

    public $createdByName;

    public $lastChangedByName;

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