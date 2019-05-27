<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;
use Inventory\Domain\Warehouse\Transaction\GenericWarehouseTransaction;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends GenericWarehouseTransaction
{
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericWarehouseTransaction::validate()
     */
    public function validate()
    {}
}