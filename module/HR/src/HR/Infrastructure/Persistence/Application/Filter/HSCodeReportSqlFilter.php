<?php
namespace Inventory\Infrastructure\Persistence\Filter;

use Inventory\Infrastructure\Persistence\Contracts\SqlFilterInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeReportSqlFilter implements SqlFilterInterface
{

    /**
     *
     * @return string
     */
    public function __toString()
    {
        // return \sprintf("ItemReportSqlFilter_%s_%s_%s_%s", $this->isActive, $this->isFixedAsset, $this->isInventoryItem, $this->itemType);
    }
}
