<?php
namespace Inventory\Domain\Warehouse;

use Inventory\Domain\Warehouse\Location\GenericLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface WarehouseCmdRepositoryInterface
{
    public function store(AbstractWarehouse $wh);
    
    public function storeLocation(GenericLocation $location);
}
