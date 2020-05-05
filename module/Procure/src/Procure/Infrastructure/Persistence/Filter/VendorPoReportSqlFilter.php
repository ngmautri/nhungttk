<?php
namespace Procure\Infrastructure\Persistence\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class VendorPoReportSqlFilter extends PoReportSqlFilter
{

    public $vendorId;

    public function __toString()
    {
        return \sprintf("VendorPoReportSqlFilter_%s_%s_%s_%s_%s_%s", $this->isActive, $this->docYear, $this->docMonth, $this->docStatus, $this->balance, $this->vendorId);
    }
}
