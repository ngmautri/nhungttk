<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Contract\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemReportSqlFilter implements SqlFilterInterface
{

    public $isActive;

    public $docStatus;

    public $balance;

    public function __toString()
    {
        return \sprintf("ItemReportSqlFilter_%s_%s_%s_%s_%s", $this->isActive);
    }
}
