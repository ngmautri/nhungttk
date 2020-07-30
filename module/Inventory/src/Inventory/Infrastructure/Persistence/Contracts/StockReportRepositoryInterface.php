<?php
namespace Inventory\Infrastructure\Persistence\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface StockReportRepositoryInterface
{

    public function getFifoLayer(SqlFilterInterface $filter, $sort_by, $sort, $limit, $offset);

    public function getOnHandQuantity($warehouseId, $itemId, $movementDate);
}
