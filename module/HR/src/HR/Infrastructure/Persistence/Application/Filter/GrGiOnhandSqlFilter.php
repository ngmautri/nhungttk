<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrGiOnhandSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $fromDate;

    public $toDate;

    public $item;

    public $warehouseId;

    public function __toString()
    {
        $f = "TrxRowReportSqlFilter_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s";
        return \sprintf($f, $this->getWarehouseId(), $this->item, $this->isActive, $this->docYear, $this->docMonth, $this->movementType, $this->docStatus, $this->fromDate, $this->toDate, $this->flow);
    }
}
