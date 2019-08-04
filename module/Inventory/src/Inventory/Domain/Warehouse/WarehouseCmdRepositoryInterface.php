<?php
namespace Inventory\Domain\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface WarehouseCmdRepositoryInterface
{
    public function store(AbstractWarehouse $wh);
}
