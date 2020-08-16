<?php
namespace Inventory\Application\Specification\Inventory;

use Inventory\Infrastructure\Persistence\Doctrine\StockReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\StockOnhandReportSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnhandQuantityAtLocationSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $wareHouseId = null;
        if (isset($subject["warehouseId"])) {
            $wareHouseId = $subject["warehouseId"];
        }

        $locationId = null;
        if (isset($subject["locationId"])) {
            $locationId = $subject["locationId"];
        }

        $itemId = null;
        if (isset($subject["itemId"])) {
            $itemId = $subject["itemId"];
        }

        $movementDate = null;
        if (isset($subject["movementDate"])) {
            $movementDate = $subject["movementDate"];
        }

        $docQuantity = null;
        if (isset($subject["docQuantity"])) {
            $docQuantity = $subject["docQuantity"];
        }

        if ($this->doctrineEM == null || $itemId == null || $wareHouseId == null || $movementDate == null || $docQuantity == null) {
            return false;
        }

        $rep = new StockReportRepositoryImpl($this->getDoctrineEM());
        $filter = new StockOnhandReportSqlFilter();
        $filter->setItemId($itemId);
        $filter->setWarehouseId($wareHouseId);
        $filter->setLocationId($locationId);
        $filter->setCheckingDate($movementDate);
        $onhand = $rep->getOnHandQuantity($filter);

        return $onhand >= $docQuantity;
    }
}