<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRDetailsSnapshot extends PRSnapshot
{

    public $warehouseName;

    public $warehouseCode;

    public $createdByName;

    public $lastChangedByName;

    public $totalRows;

    public $totalActiveRows;

    public $maxRowNumber;

    public $completedRows;
}