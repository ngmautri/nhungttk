<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends GenericTransaction
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::validate()
     */
    public function validate()
    {}
}